<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.payrolls`)" class="p-0" />
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
                    {{ $t(`menu.payrolls`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <PayrollSidebar :users="table.selectedRowKeys" />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('payrolls_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-space>
                                    <a-button
                                        type="primary"
                                        @click="regenerate"
                                        v-if="table.selectedRowKeys.length > 0"
                                        :disabled="extraFilters.month == undefined"
                                    >
                                        <ReloadOutlined />
                                        {{ $t("payroll.regenerate") }}
                                    </a-button>
                                    <a-button
                                        type="primary"
                                        @click="generate"
                                        v-else
                                        :disabled="extraFilters.month == undefined"
                                    >
                                        <SendOutlined />
                                        {{ $t("payroll.generate") }}
                                    </a-button>
                                </a-space>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('payrolls_delete') ||
                                        permsArray.includes('admin'))
                                "
                                type="primary"
                                @click="showSelectedDeleteConfirm"
                                danger
                            >
                                <template #icon><DeleteOutlined /></template>
                                {{ $t("common.delete") }}
                            </a-button>
                            <template
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('payrolls_edit') ||
                                        permsArray.includes('admin'))
                                "
                            >
                                <PayrollStatus
                                    @onSuccess="setUrlData"
                                    :payrolls="table.selectedRowKeys"
                                />
                            </template>
                        </a-space>
                    </a-col>

                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col
                                v-if="
                                    permsArray.includes('payrolls_view') ||
                                    permsArray.includes('admin')
                                "
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="6"
                                :xl="6"
                            >
                                <a-select
                                    v-model:value="filters.user_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('payroll.user_id'),
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
                                <a-date-picker
                                    v-model:value="extraFilters.year"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('holiday.year'),
                                        ])
                                    "
                                    picker="year"
                                    @change="setUrlData"
                                    style="width: 100%"
                                    :allowClear="false"
                                />
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                                <a-select
                                    v-model:value="extraFilters.month"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('holiday.month'),
                                        ])
                                    "
                                    :allowClear="true"
                                    style="width: 100%"
                                    optionFilterProp="title"
                                    show-search
                                    @change="setUrlData"
                                >
                                    <a-select-option
                                        v-for="month in monthArrays"
                                        :key="month.name"
                                        :value="month.value"
                                    >
                                        {{ month.name }}
                                    </a-select-option>
                                </a-select>
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

                <a-alert
                    v-if="
                        permsArray.includes('basic_salaries_add') ||
                        permsArray.includes('basic_salaries_edit') ||
                        permsArray.includes('admin')
                    "
                    class="mb-20"
                    :message="$t('payroll.setup_basic_salary_to_generate_payroll')"
                    type="info"
                    show-icon
                >
                    <template #action>
                        <a-button
                            size="small"
                            type="primary"
                            @click="
                                $router.push({ name: 'admin.hrm.basic_salaries.index' })
                            "
                        >
                            <DollarCircleOutlined />
                            {{ $t("payroll.basic_salary_setup") }}
                        </a-button>
                    </template>
                </a-alert>

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
                                    <template v-if="column.dataIndex == 'user_id'">
                                        {{ record.user.name }}
                                    </template>
                                    <template v-if="column.dataIndex == 'payment_date'">
                                        {{
                                            record.payment_date != null
                                                ? formatDate(record.payment_date)
                                                : "-"
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex == 'month'">
                                        {{ getMonthNameByNumber(record.month) }}
                                        {{ record.year }}
                                    </template>
                                    <template v-if="column.dataIndex == 'net_salary'">
                                        {{ formatAmountCurrency(record.net_salary) }}
                                    </template>
                                    <template v-if="column.dataIndex == 'status'">
                                        <div v-if="record.status == 'generated'">
                                            <a-tag color="yellow">
                                                {{ $t(`payroll.generated`) }}
                                            </a-tag>
                                        </div>
                                        <div v-if="record.status == 'paid'">
                                            <a-tag color="green">
                                                {{ $t(`common.paid`) }}
                                            </a-tag>
                                        </div>
                                    </template>

                                    <template v-if="column.dataIndex === 'action'">
                                        <a-space>
                                            <a-button
                                                type="primary"
                                                @click="() => editItem(record)"
                                                style="margin-left: 4px"
                                            >
                                                <template #icon><EyeOutlined /></template>
                                            </a-button>
                                            <!-- <a-button
                                                v-if="
                                                    permsArray.includes(
                                                        'payrolls_edit'
                                                    ) || permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="editItem(record)"
                                            >
                                                <template #icon
                                                    ><EditOutlined
                                                /></template>
                                            </a-button> -->
                                            <a-button
                                                v-if="
                                                    permsArray.includes(
                                                        'payrolls_delete'
                                                    ) || permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="showDeleteConfirm(record.xid)"
                                            >
                                                <template #icon
                                                    ><DeleteOutlined
                                                /></template>
                                            </a-button>
                                        </a-space>
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
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    CloseOutlined,
    CheckOutlined,
    SendOutlined,
    ReloadOutlined,
    EyeOutlined,
    DollarCircleOutlined,
} from "@ant-design/icons-vue";
import fields from "./fields";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import { notification } from "ant-design-vue";
import dayjs from "dayjs";
import PayrollSidebar from "../PayrollSidebar.vue";
import hrmManagement from "../../../../../common/composable/hrmManagement";
import PayrollStatus from "./PayrollStatus.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        SendOutlined,
        ReloadOutlined,
        DollarCircleOutlined,
        AddEdit,
        AdminPageHeader,
        CloseOutlined,
        CheckOutlined,
        PayrollSidebar,
        hrmManagement,
        PayrollStatus,
        EyeOutlined,
    },
    setup() {
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            url,
        } = fields();
        const crudVariables = crud();
        const { t } = useI18n();
        const { permsArray, appSetting, formatDate, formatAmountCurrency } = common();
        const filters = ref({ user_id: undefined });
        const extraFilters = ref({ month: dayjs().format("MM"), year: dayjs() });
        const allUsers = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const { getMonthNameByNumber, monthArrays } = hrmManagement();

        onMounted(() => {
            setUrlData();
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "payroll";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                filters,
                extraFilters: {
                    month: extraFilters.value.month,
                    year: extraFilters.value.year.format("YYYY"),
                },
                url,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };
        const generate = () => {
            axiosAdmin
                .post("payrolls/generate", {
                    month: extraFilters.value.month,
                    year: extraFilters.value.year.format("YYYY"),
                })
                .then((successResponse) => {
                    setUrlData();
                    notification.success({
                        message: t("common.success"),
                        description: t(`common.generated`),
                        placement: "bottomRight",
                    });
                });
        };

        const regenerate = () => {
            axiosAdmin
                .post("payrolls/generate", {
                    month: extraFilters.value.month,
                    year: extraFilters.value.year.format("YYYY"),
                    payrolls: crudVariables.table.selectedRowKeys,
                })
                .then((successResponse) => {
                    setUrlData();
                    notification.success({
                        message: t("common.success"),
                        description: t(`common.Regenerated`),
                        placement: "bottomRight",
                    });
                });
        };

        return {
            columns,
            ...crudVariables,
            filterableColumns,
            permsArray,
            appSetting,
            formatDate,
            setUrlData,
            allUsers,
            filters,
            extraFilters,
            generate,
            regenerate,
            formatAmountCurrency,
            getMonthNameByNumber,
            monthArrays,
        };
    },
};
</script>
