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
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('increment_promotion.type')"
                        name="type"
                        :help="rules.type ? rules.type.message : null"
                        :validateStatus="rules.type ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="formData.type"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('increment_promotion.type'),
                                ])
                            "
                        >
                            <a-select-option value="increment">
                                {{ $t("increment_promotion.increment") }}
                            </a-select-option>
                            <a-select-option value="promotion">
                                {{ $t("increment_promotion.promotion") }}
                            </a-select-option>
                            <a-select-option value="increment_promotion">
                                {{ $t("increment_promotion.increment_promotion") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('increment_promotion.user_id')"
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
                                        $t('increment_promotion.user_id'),
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
            <a-row
                :gutter="16"
                v-if="
                    formData.type == 'promotion' || formData.type == 'increment_promotion'
                "
            >
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('increment_promotion.current_designation_id')"
                        name="current_designation_id"
                        :help="
                            rules.current_designation_id
                                ? rules.current_designation_id.message
                                : null
                        "
                        :validateStatus="rules.current_designation_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.current_designation_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('increment_promotion.current_designation_id'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="designation in designations"
                                    :key="designation.xid"
                                    :value="designation.xid"
                                >
                                    {{ designation.name }}
                                </a-select-option>
                            </a-select>
                            <DesignationAddButton @onAddSuccess="designationAdded" />
                        </span>
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('increment_promotion.promoted_designation_id')"
                        name="promoted_designation_id"
                        :help="
                            rules.promoted_designation_id
                                ? rules.promoted_designation_id.message
                                : null
                        "
                        :validateStatus="rules.promoted_designation_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.promoted_designation_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('increment_promotion.promoted_designation_id'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="designation in designations"
                                    :key="designation.xid"
                                    :value="designation.xid"
                                >
                                    {{ designation.name }}
                                </a-select-option>
                            </a-select>
                            <DesignationAddButton @onAddSuccess="designationAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="
                        formData.type == 'increment' ||
                        formData.type == 'increment_promotion'
                    "
                >
                    <a-form-item
                        :label="$t('increment_promotion.net_salary')"
                        name="net_salary"
                        :help="rules.net_salary ? rules.net_salary.message : null"
                        :validateStatus="rules.net_salary ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="formData.net_salary"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('increment_promotion.net_salary'),
                                ])
                            "
                            style="width: 100%"
                        >
                            <template #addonBefore>
                                {{ appSetting.currency.symbol }}
                            </template></a-input-number
                        >
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('increment_promotion.date')"
                        name="date"
                        :help="rules.date ? rules.date.message : null"
                        :validateStatus="rules.date ? 'error' : null"
                        class="required"
                    >
                        <DateTimePicker
                            :dateTime="formData.date"
                            @dateTimeChanged="
                                (changedDateTime) => (formData.date = changedDateTime)
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row
                v-if="
                    addEditType == 'add' &&
                    (formData.type == 'increment' ||
                        formData.type == 'increment_promotion') &&
                    (permsArray.includes('basic_salaries_edit') ||
                        permsArray.includes('designations_edit') ||
                        permsArray.includes('admin'))
                "
                class="mt-5 mb-20"
                :gutter="16"
            >
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <label
                        v-if="
                            (formData.type == 'increment' ||
                                formData.type == 'increment_promotion') &&
                            (permsArray.includes('basic_salaries_edit') ||
                                permsArray.includes('admin'))
                        "
                        class="form-check form-check-custom me-5 me-lg-20"
                    >
                        <a-checkbox v-model:checked="newData.update_basic_salary">
                            {{ $t("increment_promotion.update_basic_salary") }}
                        </a-checkbox>
                    </label>

                    <label
                        v-if="
                            (formData.type == 'promotion' ||
                                formData.type == 'increment_promotion') &&
                            (permsArray.includes('designations_edit') ||
                                permsArray.includes('admin'))
                        "
                        class="form-check form-check-custom me-5 me-lg-20"
                    >
                        <a-checkbox v-model:checked="newData.update_designation">
                            {{ $t("increment_promotion.update_designation") }}
                        </a-checkbox>
                    </label>
                </a-col></a-row
            >
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('increment_promotion.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('increment_promotion.description'),
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
import StaffMemberAddButton from "../../../../views/users/StaffAddButton.vue";
import DesignationAddButton from "../../staff-members/designations/AddButton.vue";
import DateTimePicker from "../../../../../common/components/common/calendar/DateTimePicker.vue";

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
        DesignationAddButton,
        DateTimePicker,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray } = common();
        const allStaffMembers = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const designations = ref([]);
        const designationUrl = "designations?fields=id,xid,name&limit=10000";
        const newData = ref({
            update_basic_salary: false,
            update_designation: false,
        });

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const designationPromise = axiosAdmin.get(designationUrl);
            Promise.all([staffMemberPromise, designationPromise]).then(
                ([staffMemberResponse, designationResponse]) => {
                    allStaffMembers.value = staffMemberResponse.data;
                    designations.value = designationResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: { ...newData.value, ...props.formData },
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

        const designationAdded = () => {
            axiosAdmin.get(designationUrl).then((response) => {
                designations.value = response.data;
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
            designationAdded,
            staffMemberAdded,
            allStaffMembers,
            designations,
            newData,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
