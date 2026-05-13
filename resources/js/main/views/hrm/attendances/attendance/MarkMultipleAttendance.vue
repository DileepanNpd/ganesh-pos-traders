<template>
    <a-button type="primary" @click="addAttendance">
        <PlusOutlined />
        {{ $t("leave.add_multiple") }}
    </a-button>
    <a-drawer :width="drawerWidth" :open="visible" :title="pageTitle" @close="onClose">
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave.department')"
                        name="department"
                        :help="rules.department_id ? rules.department_id.message : null"
                        :validateStatus="rules.department_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.department_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('leave.department'),
                                    ])
                                "
                                :allowClear="true"
                                @change="getDepartmentStaff(formData.department_id)"
                            >
                                <a-select-option
                                    v-for="department in departments"
                                    :key="department.xid"
                                    :value="department.xid"
                                >
                                    {{ department.name }}
                                </a-select-option>
                            </a-select>
                            <DepartmentAddButton @onAddSuccess="departmentAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave.employees')"
                        name="employees"
                        :help="rules.employee_id ? rules.employee_id.message : null"
                        :validateStatus="rules.employee_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.employee_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('leave.employees'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="user in employees"
                                    :key="user.xid"
                                    :value="user.xid"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                            <StaffAddButton @onAddSuccess="staffAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave.mark_attendance')"
                        name="mark_attendance"
                    >
                        <a-radio-group
                            v-model:value="formData.mark_attendance"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="'month'">
                                {{ $t("common.month") }}
                            </a-radio-button>
                            <a-radio-button :value="'date'">
                                {{ $t("common.date") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="formData.mark_attendance == 'month'"
                >
                    <a-form-item
                        :label="$t('leave.clock_in_month')"
                        name="clock_in_month"
                        :help="
                            rules.attendance_month ? rules.attendance_month.message : null
                        "
                        :validateStatus="rules.attendance_month ? 'error' : null"
                        class="required"
                    >
                        <a-date-picker
                            v-model:value="formData.attendance_month"
                            valueFormat="YYYY-MM"
                            picker="month"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="formData.mark_attendance == 'date'"
                >
                    <a-form-item
                        :label="$t('leave.multiple_date')"
                        name="multiple_date"
                        :help="
                            rules.attendance_date ? rules.attendance_date.message : null
                        "
                        :validateStatus="rules.attendance_date ? 'error' : null"
                        class="required"
                    >
                        <a-range-picker
                            v-model:value="formData.attendance_date"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave.clock_in')"
                        name="clock_in"
                        :help="rules.clock_in ? rules.clock_in.message : null"
                        :validateStatus="rules.clock_in ? 'error' : null"
                        class="required"
                    >
                        <a-time-picker
                            v-model:value="formData.clock_in"
                            valueFormat="h:mm A"
                            style="width: 100%"
                            use12-hours
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave.clock_out')"
                        name="clock_out"
                        :help="rules.clock_out ? rules.clock_out.message : null"
                        :validateStatus="rules.clock_out ? 'error' : null"
                        class="required"
                    >
                        <a-time-picker
                            v-model:value="formData.clock_out"
                            valueFormat="h:mm A"
                            style="width: 100%"
                            use12-hours
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('leave.late')"
                        name="late"
                        :help="rules.late ? rules.late.message : null"
                        :validateStatus="rules.late ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.late"
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
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('leave.half_day')"
                        name="half_day"
                        :help="rules.half_day ? rules.half_day.message : null"
                        :validateStatus="rules.half_day ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.half_day"
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
                    v-if="formData.half_day == '1'"
                >
                    <a-form-item
                        :label="$t('leave.leave_type')"
                        name="leave_type"
                        :help="rules.leave_type ? rules.leave_type.message : null"
                        :validateStatus="rules.leave_type ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.leave_type"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('leave.leave_type'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="leave in leaveTypes"
                                    :key="leave.xid"
                                    :value="leave.xid"
                                >
                                    {{ leave.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <a-form-item name="attendance_overwrite">
                        <a-checkbox
                            v-model:value="formData.attendance_overwrite"
                            size="large"
                        >
                            {{ $t("leave.attendance_overwrite") }}
                        </a-checkbox>
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
                    {{ $t("common.create") }}
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
import StaffAddButton from "../../staff-members/staff/StaffAddButton.vue";
import DepartmentAddButton from "../../staff-members/departments/AddButton.vue";
import { filter, forEach } from "lodash-es";

export default defineComponent({
    components: {
        PlusOutlined,
        SaveOutlined,
        StaffAddButton,
        DepartmentAddButton,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const visible = ref(false);
        const formData = ref({
            department_id: undefined,
            employee_id: undefined,
            mark_attendance: "month",
            late: 0,
            half_day: 0,
            attendance_overwrite: 0,
        });
        const pageTitle = ref("Attendance Details");
        const departments = ref([]);
        const departmentUrl = "departments?limit=10000";
        const employees = ref([]);
        const leaveTypes = ref([]);
        const leaveTypeUrl = "leave-types?fields=id,xid,name&limit=10000";
        const users = ref([]);
        const userUrl =
            "users?fields=id,xid,name,department_id,x_department_id&limit=10000";

        onMounted(() => {
            const departmentsPromise = axiosAdmin.get(departmentUrl);
            const usersPromise = axiosAdmin.get(userUrl);
            const leaveTypesPromise = axiosAdmin.get(leaveTypeUrl);

            Promise.all([departmentsPromise, usersPromise, leaveTypesPromise]).then(
                ([departmentResponse, usersResponse, leaveTypesResponse]) => {
                    departments.value = departmentResponse.data;
                    users.value = usersResponse.data;
                    employees.value = usersResponse.data;
                    leaveTypes.value = leaveTypesResponse.data;
                }
            );
        });

        const getDepartmentStaff = (xid) => {
            employees.value = [];
            if (xid != undefined) {
                employees.value = filter(users.value, ["x_department_id", xid]);
            } else {
                forEach(users.value, (user) => {
                    employees.value.push(user);
                });
            }
        };

        const addAttendance = () => {
            visible.value = true;
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: `add-users-attendance`,
                data: formData.value,
                successMessage: props.successMessage,
                success: (res) => {
                    visible.value = false;
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            visible.value = false;
        };

        const departmentAdded = () => {
            axiosAdmin.get(departmentUrl).then((response) => {
                departments.value = response.data;
            });
        };

        const staffAdded = () => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
            });
        };

        return {
            onSubmit,
            onClose,
            getDepartmentStaff,

            users,
            employees,
            departments,
            leaveTypes,
            visible,
            formData,
            addAttendance,
            rules,
            loading,
            pageTitle,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            departmentAdded,
            staffAdded,
        };
    },
});
</script>
