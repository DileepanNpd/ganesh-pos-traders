<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.pre_payments`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.hrm`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.pre_payments`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <PayrollSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('pre_payments_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("pre_payment.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('pre_payments_delete') ||
                                        permsArray.includes('admin'))
                                "
                                type="primary"
                                @click="showSelectedDeleteConfirm"
                                danger
                            >
                                <template #icon><DeleteOutlined /></template>
                                {{ $t("common.delete") }}
                            </a-button>
                        </a-space>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col
                                v-if="
                                    permsArray.includes('pre_payments_view') ||
                                    permsArray.includes('admin')
                                "
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="8"
                                :xl="8"
                            >
                                <a-select
                                    v-model:value="extraFilters.user_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('leave.user'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option
                                        v-for="allUser in allUsers"
                                        :key="allUser.xid"
                                        :value="allUser.xid"
                                        :title="allUser.name"
                                    >
                                        {{ allUser.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                                <a-select
                                    v-model:value="filters.payment_mode_id"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('pre_payment.payment_mode_id'),
                                        ])
                                    "
                                    :allowClear="true"
                                    style="width: 100%"
                                    optionFilterProp="title"
                                    show-search
                                    @change="setUrlData"
                                >
                                    <a-select-option
                                        v-for="paymentMode in paymentModes"
                                        :key="paymentMode.xid"
                                        :title="paymentMode.name"
                                        :value="paymentMode.xid"
                                    >
                                        {{ paymentMode.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                                <a-range-picker
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    v-model:value="extraFilters.dates"
                                    :style="{ width: '100%' }"
                                    @change="setUrlData"
                                />
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </admin-page-filters>

            <admin-page-table-content>
                <AddEdit
                    :addEditType="addEditType"
                    :visible="addEditVisible"
                    :url="addEditUrl"
                    @addEditSuccess="addEditSuccess"
                    @closed="onCloseAddEdit"
                    :formData="formData"
                    :data="viewData"
                    :pageTitle="pageTitle"
                    :successMessage="successMessage"
                />

                <a-row>
                    <a-col :span="24">
                        <div class="table-responsive">
                            <a-table
                                :row-selection="{
                                    selectedRowKeys: table.selectedRowKeys,
                                    onChange: onRowSelectChange,
                                    getCheckboxProps: (record) => ({
                                        disabled: false,
                                        name: record.xid,
                                    }),
                                }"
                                :columns="columns"
                                :row-key="(record) => record.xid"
                                :data-source="table.data"
                                :pagination="table.pagination"
                                :loading="table.loading"
                                @change="handleTableChange"
                                bordered
                                size="middle"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex == 'date_time'">
                                        {{ formatDateTime(record.date_time) }}
                                    </template>
                                    <template v-if="column.dataIndex == 'user_id'">
                                        {{ record.user.name }}
                                    </template>
                                    <template v-if="column.dataIndex == 'amount'">
                                        {{ formatAmountCurrency(record.amount) }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex == 'payment_mode_id'"
                                    >
                                        {{ record.payment_mode.name }}
                                    </template>
                                    <template v-if="column.dataIndex == 'deduct_month'">
                                        {{ getMonthNameByNumber(record.payroll_month) }}
                                        {{ record.payroll_year }}
                                    </template>

                                    <template v-if="column.dataIndex === 'action'">
                                        <a-button
                                            v-if="
                                                permsArray.includes(
                                                    'pre_payments_edit'
                                                ) || permsArray.includes('admin')
                                            "
                                            type="primary"
                                            @click="editItem(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><EditOutlined /></template>
                                        </a-button>
                                        <a-button
                                            v-if="
                                                permsArray.includes(
                                                    'pre_payments_delete'
                                                ) || permsArray.includes('admin')
                                            "
                                            type="primary"
                                            @click="showDeleteConfirm(record.xid)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><DeleteOutlined /></template>
                                        </a-button>
                                    </template>
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>
<script>
import { onMounted, ref } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import fields from "./fields";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import DateRangePicker from "../../../../../common/components/common/calendar/DateRangePicker.vue";
import PayrollSidebar from "../PayrollSidebar.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        DateRangePicker,
        PayrollSidebar,
    },
    setup() {
        const { t } = useI18n();
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const {
            permsArray,
            appSetting,
            formatDate,
            formatAmountCurrency,
            formatDateTime,
        } = common();
        const { getMonthNameByNumber } = hrmManagement();
        const filters = ref({
            payment_mode_id: undefined,
        });
        const extraFilters = ref({ dates: [], user_id: undefined });
        const paymentModes = ref([]);
        const allUsers = ref([]);
        const staffMembersUrl = "users?limit=10000";

        onMounted(() => {
            if (
                permsArray.value.includes("pre_payments_edit") ||
                permsArray.value.includes("pre_payments_delete") ||
                permsArray.value.includes("admin")
            ) {
                columns.value = [
                    ...columns.value,
                    {
                        title: t("common.action"),
                        dataIndex: "action",
                    },
                ];
            }

            setUrlData();
            getModeData();

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "pre_payment";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url:
                    "pre-payments?fields=id,xid,user_id,x_user_id,user{id,xid,name},payment_mode_id,x_payment_mode_id,paymentMode{id,xid,name},amount,notes,payroll_year,payroll_month,date_time,deduct_from_payroll",
                filters,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        const getModeData = () => {
            const paymentModesPromise = axiosAdmin.get("payment-modes?limit=10000");
            const usersPromise = axiosAdmin.get(staffMembersUrl);

            Promise.all([paymentModesPromise, usersPromise]).then(
                ([paymentModesResponse, userResponse]) => {
                    paymentModes.value = paymentModesResponse.data;
                    allUsers.value = userResponse.data;
                }
            );
        };

        return {
            columns,
            ...crudVariables,
            filterableColumns,
            permsArray,
            appSetting,
            formatDate,
            formatDateTime,
            filters,
            extraFilters,
            paymentModes,
            setUrlData,
            formatAmountCurrency,
            allUsers,
            getMonthNameByNumber,
        };
    },
};
</script>
