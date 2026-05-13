<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.appreciations`)" class="p-0" />
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
                    {{ $t(`menu.appreciations`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <AppreciationSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes('appreciations_create') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("appreciation.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes('appreciations_delete') ||
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
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="6"
                                :xl="6"
                                v-if="
                                    permsArray.includes('appreciations_view') ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-select
                                    v-model:value="extraFilters.user_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('appreciation.user'),
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
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <a-select
                                    v-model:value="filters.award_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('appreciation.award'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option
                                        v-for="award in awards"
                                        :key="award.xid"
                                        :value="award.xid"
                                        :title="award.name"
                                    >
                                        {{ award.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                                <DateRangePicker
                                    @dateTimeChanged="
                                        (changedDateTime) => {
                                            extraFilters.date = changedDateTime;
                                            setUrlData();
                                        }
                                    "
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
                    @awardAdded="onAwardAdded"
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
                                    <template v-if="column.dataIndex === 'price_given'">
                                        <ul
                                            v-for="price in record.price_given"
                                            :key="price.price_given"
                                        >
                                            <li>{{ price.price_given }}</li>
                                        </ul>
                                    </template>
                                    <template v-if="column.dataIndex === 'user_id'">
                                        {{ record.user?.name }}
                                    </template>
                                    <template v-if="column.dataIndex === 'award_id'">
                                        {{ record.award?.name }}
                                    </template>
                                    <template v-if="column.dataIndex === 'date'">
                                        {{ formatDateTime(record.date) }}
                                    </template>
                                    <template v-if="column.dataIndex === 'price_amount'">
                                        {{ formatAmountCurrency(record.price_amount) }}
                                    </template>
                                    <template v-if="column.dataIndex == 'active'">
                                        {{
                                            record.active
                                                ? $t("common.yes")
                                                : $t("common.no")
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'payout_type'">
                                        {{
                                            record.payout_type == "monthly"
                                                ? $t("appreciation.monthly")
                                                : $t("appreciation.daily")
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-button
                                            v-if="
                                                permsArray.includes(
                                                    'appreciations_edit'
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
                                                    'appreciations_delete'
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
import { onMounted, ref, watch } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import fields from "./fields";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import DateRangePicker from "../../../../../common/components/common/calendar/DateRangePicker.vue";
import AppreciationSidebar from "../AppreciationSidebar.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        DateRangePicker,

        AppreciationSidebar,
    },
    setup() {
        const { t } = useI18n();
        const {
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            url,
        } = fields();
        const crudVariables = crud();
        const { permsArray, appSetting, formatDateTime, formatAmountCurrency } = common();
        const allUsers = ref([]);
        const awards = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const awardsUrl = "awards?limit=10000";
        const filters = ref({ award_id: undefined });
        const extraFilters = ref({
            date: [],
            user_id: undefined,
        });

        onMounted(() => {
            if (
                permsArray.value.includes("awards_edit") ||
                permsArray.value.includes("awards_delete") ||
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

            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const awardPromise = axiosAdmin.get(awardsUrl);
            Promise.all([staffMemberPromise, awardPromise]).then(
                ([staffMemberResponse, awardResponse]) => {
                    allUsers.value = staffMemberResponse.data;
                    awards.value = awardResponse.data;
                }
            );

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "appreciation";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];

            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url,
                filters,
                extraFilters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

        const onAwardAdded = () => {
            axiosAdmin.get(awardsUrl).then((response) => {
                awards.value = response.data;
            });
        };

        return {
            allUsers,
            awards,
            filters,
            extraFilters,
            staffMembersUrl,
            awardsUrl,
            columns,
            ...crudVariables,
            filterableColumns,
            permsArray,
            appSetting,
            formatDateTime,
            setUrlData,
            onAwardAdded,
            formatAmountCurrency,
        };
    },
};
</script>
