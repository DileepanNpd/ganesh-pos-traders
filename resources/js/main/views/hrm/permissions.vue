<template>
    <tr>
        <td :colspan="3">&nbsp;</td>
    </tr>
    <tr class="text-center">
        <td :colspan="3">
            <b> {{ $t("common.hrm_permissions") }} </b>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.shifts") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['shifts_view']">
                        {{ $t("common.view") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['shifts_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['shifts_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['shifts_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.departments") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['departments_view']">
                        {{ $t("common.view") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['departments_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['departments_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['departments_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.designations") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['designations_view']">
                        {{ $t("common.view") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['designations_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['designations_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['designations_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>

    <tr>
        <td class="text-gray-800">
            {{ $t("menu.leaves_types") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leave_types_view']">
                        {{ $t("common.view") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leave_types_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leave_types_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leave_types_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.leaves") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leaves_assign_to_all']">
                        {{ $t("common.assign_to_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leaves_edit_all']">
                        {{ $t("common.edit_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leaves_delete_all']">
                        {{ $t("common.delete_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['leaves_approve_reject']">
                        {{ $t("common.approve_reject") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>

    <tr>
        <td class="text-gray-800">
            {{ $t("menu.holidays") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['holidays_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['holidays_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['holidays_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['mark_weekend_holiday']">
                        {{ $t("role.mark_weekend") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.awards") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['awards_view']">
                        {{ $t("common.view") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['awards_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['awards_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['awards_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.appreciations") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['appreciations_view']">
                        {{ $t("common.view_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['appreciations_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['appreciations_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['appreciations_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>

    <tr>
        <td class="text-gray-800">
            {{ $t("menu.payrolls") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['payrolls_view']">
                        {{ $t("common.view_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['payrolls_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['payrolls_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['payrolls_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.pre_payments") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['pre_payments_view']">
                        {{ $t("common.view_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['pre_payments_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['pre_payments_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['pre_payments_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.attendances") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['attendances_view']">
                        {{ $t("common.view_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['attendances_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['attendances_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['attendances_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.basic_salaries") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['basic_salaries_view']">
                        {{ $t("common.view_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['basic_salaries_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['basic_salaries_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['basic_salaries_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.increments_promotions") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['increments_promotions_view']">
                        {{ $t("common.view_all") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['increments_promotions_create']">
                        {{ $t("common.add") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['increments_promotions_edit']">
                        {{ $t("common.edit") }}
                    </a-checkbox>
                </label>
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['increments_promotions_delete']">
                        {{ $t("common.delete") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="text-gray-800">
            {{ $t("menu.hrm_settings") }}
        </td>
        <td>
            <div class="d-flex">
                <label class="form-check form-check-custom me-5 me-lg-20">
                    <a-checkbox :value="permissions['hrm_settings']">
                        {{ $t("menu.hrm_settings") }}
                    </a-checkbox>
                </label>
            </div>
        </td>
    </tr>
</template>

<script>
import { TeamOutlined } from "@ant-design/icons-vue";

export default {
    props: ["permissions"],
    components: {},
    setup(props, { emit }) {
        return {};
    },
};
</script>
