<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="24"
                    v-if="
                        permsArray.includes('admin') ||
                        permsArray.includes('leaves_assign_to_all')
                    "
                >
                    <a-form-item
                        :label="$t('leave.user_id')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.user_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('leave.user_id'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="allStaffMember in allStaffMembers"
                                    :key="allStaffMember.xid"
                                    :value="allStaffMember.xid"
                                >
                                    {{ allStaffMember.name }}
                                </a-select-option>
                            </a-select>
                            <StaffMemberAddButton @onAddSuccess="staffMemberAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="12">
                    <a-form-item
                        :label="$t('leave.leave_type')"
                        name="leave_type_id"
                        :help="rules.leave_type_id ? rules.leave_type_id.message : null"
                        :validateStatus="rules.leave_type_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.leave_type_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('leave.leave_type'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="allLeaveType in allLeaveTypes"
                                    :key="allLeaveType.xid"
                                    :value="allLeaveType.xid"
                                >
                                    {{ allLeaveType.name }}
                                </a-select-option>
                            </a-select>
                            <LeaveTypeAddButton @onAddSuccess="leaveTypeAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="12">
                    <a-form-item
                        :label="$t('holiday.date')"
                        name="date"
                        :help="rules.date ? rules.date.message : null"
                        :validateStatus="rules.date ? 'error' : null"
                        class="required"
                    >
                        <a-range-picker
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            v-model:value="formData.date"
                            :style="{ width: '100%' }"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave.is_half_day')"
                        name="is_half_day"
                        :help="rules.is_half_day ? rules.is_half_day.message : null"
                        :validateStatus="rules.is_half_day ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.is_half_day"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="
                        permsArray.includes('admin') ||
                        permsArray.includes('leaves_approve_reject')
                    "
                >
                    <a-form-item
                        :label="$t('user.status')"
                        name="status"
                        :help="rules.status ? rules.status.message : null"
                        :validateStatus="rules.status ? 'error' : null"
                    >
                        <a-select
                            v-model:value="formData.status"
                            :placeholder="
                                $t('common.select_default_text', [$t('common.status')])
                            "
                        >
                            <a-select-option value="pending">
                                {{ $t("common.pending") }}
                            </a-select-option>
                            <a-select-option value="approved">
                                {{ $t("common.approved") }}
                            </a-select-option>
                            <a-select-option value="rejected">
                                {{ $t("common.rejected") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('leave.reason')"
                        name="reason"
                        :help="rules.reason ? rules.reason.message : null"
                        :validateStatus="rules.reason ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.reason"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave.reason'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, onMounted, ref } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import common from "../../../../../common/composable/common";
import StaffMemberAddButton from "../../../../../main/views/users/StaffAddButton.vue";
import UploadFile from "../../../../../common/core/ui/file/UploadFile.vue";
import LeaveTypeAddButton from "../leave-types/AddButton.vue";
import DateRangePicker from "../../../../../common/components/common/calendar/DateRangePicker.vue";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        StaffMemberAddButton,
        LeaveTypeAddButton,
        UploadFile,
        DateRangePicker,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray } = common();
        const allStaffMembers = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const allLeaveTypes = ref([]);
        const leaveTypeUrl = "leave-types?fields=id,xid,name&limit=10000";

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const leaveTypePromise = axiosAdmin.get(leaveTypeUrl);
            Promise.all([staffMemberPromise, leaveTypePromise]).then(
                ([staffMemberResponse, leaveTypeResponse]) => {
                    allStaffMembers.value = staffMemberResponse.data;
                    allLeaveTypes.value = leaveTypeResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: {
                    ...props.formData,
                    start_date: props.formData.date ? props.formData.date[0] : "",
                    end_date: props.formData.date ? props.formData.date[1] : "",
                },
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const staffMemberAdded = () => {
            axiosAdmin.get(staffMembersUrl).then((response) => {
                allStaffMembers.value = response.data;
            });
        };

        const leaveTypeAdded = () => {
            axiosAdmin.get(leaveTypeUrl).then((response) => {
                allLeaveTypes.value = response.data;
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,

            staffMemberAdded,
            allStaffMembers,
            leaveTypeAdded,
            allLeaveTypes,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
