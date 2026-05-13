<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.increments_promotions`)" class="p-0" />
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
                    {{ $t(`menu.increments_promotions`) }}
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
                                    permsArray.includes('increments_promotions_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("increment_promotion.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes(
                                        'increments_promotions_delete'
                                    ) ||
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
                                    permsArray.includes('increments_promotions_view') ||
                                    permsArray.includes('admin')
                                "
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="8"
                                :xl="8"
                            >
                                <a-select
                                    v-model:value="filters.user_id"
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
                        <a-tabs
                            v-model:activeKey="extraFilters.type"
                            @change="setUrlData"
                        >
                            <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                            <a-tab-pane
                                key="increment"
                                :tab="`${$t('increment_promotion.increment')}`"
                            />
                            <a-tab-pane
                                key="promotion"
                                :tab="`${$t('increment_promotion.promotion')}`"
                            />
                            <a-tab-pane
                                key="increment_promotion"
                                :tab="`${$t('increment_promotion.increment_promotion')}`"
                            />
                        </a-tabs>
                    </a-col>
                </a-row>

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
                                    <template v-if="column.dataIndex == 'net_salary'">
                                        {{
                                            record.net_salary != null
                                                ? formatAmountCurrency(record.net_salary)
                                                : "-"
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex == 'date'">
                                        {{ formatDate(record.date) }}
                                    </template>
                                    <template v-if="column.dataIndex == 'description'">
                                        {{ record.description }}
                                    </template>
                                    <template v-if="column.dataIndex == 'details'">
                                        <span
                                            v-if="
                                                record.current_designation &&
                                                record.current_designation.name
                                            "
                                        >
                                            {{
                                                $t(
                                                    "increment_promotion.current_designation_id"
                                                )
                                            }}: {{ record.current_designation.name }}
                                        </span>
                                        <br />
                                        <span
                                            v-if="
                                                record.promoted_designation &&
                                                record.promoted_designation.name
                                            "
                                        >
                                            {{
                                                $t(
                                                    "increment_promotion.promoted_designation_id"
                                                )
                                            }}:
                                            {{ record.promoted_designation.name }}
                                        </span>
                                    </template>

                                    <template v-if="column.dataIndex === 'type'">
                                        <div v-if="record.type == 'promotion'">
                                            <a-tag color="yellow">
                                                {{
                                                    $t(
                                                        `increment_promotion.${"promotion"}`
                                                    )
                                                }}
                                            </a-tag>
                                        </div>
                                        <div v-if="record.type == 'increment'">
                                            <a-tag color="green">
                                                {{
                                                    $t(
                                                        `increment_promotion.${"increment"}`
                                                    )
                                                }}
                                            </a-tag>
                                        </div>
                                        <div v-if="record.type == 'increment_promotion'">
                                            <a-tag color="green">
                                                {{
                                                    $t(
                                                        `increment_promotion.${"increment_promotion"}`
                                                    )
                                                }}
                                            </a-tag>
                                        </div>
                                    </template>

                                    <template v-if="column.dataIndex === 'action'">
                                        <a-space>
                                            <a-button
                                                v-if="
                                                    permsArray.includes(
                                                        'increments_promotions_edit'
                                                    ) || permsArray.includes('admin')
                                                "
                                                type="primary"
                                                @click="editItem(record)"
                                            >
                                                <template #icon
                                                    ><EditOutlined
                                                /></template>
                                            </a-button>
                                            <a-button
                                                v-if="
                                                    permsArray.includes(
                                                        'increments_promotions_delete'
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
import { onMounted, ref, createVNode } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    CloseOutlined,
    CheckOutlined,
} from "@ant-design/icons-vue";
import fields from "./fields";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import PayrollSidebar from "../PayrollSidebar.vue";
import DateRangePicker from "../../../../../common/components/common/calendar/DateRangePicker.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        CloseOutlined,
        CheckOutlined,
        PayrollSidebar,
        DateRangePicker,
    },
    setup() {
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const { t } = useI18n();
        const { permsArray, appSetting, formatDate, formatAmountCurrency } = common();
        const filters = ref({ user_id: undefined });
        const allUsers = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const extraFilters = ref({ dates: [], type: "all" });

        onMounted(() => {
            if (
                permsArray.value.includes("increments_promotions_edit") ||
                permsArray.value.includes("increments_promotions_delete") ||
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
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "increment_promotion";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                extraFilters,
                filters,
                url:
                    "increments-promotions?fields=id,xid,user_id,x_user_id,user{id,xid,name},promoted_designation_id,x_promoted_designation_id,promotedDesignation{id,xid,name},current_designation_id,x_current_designation_id,currentDesignation{id,xid,name},net_salary,type,description,date",
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
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
            formatAmountCurrency,
            extraFilters,
        };
    },
};
</script>
