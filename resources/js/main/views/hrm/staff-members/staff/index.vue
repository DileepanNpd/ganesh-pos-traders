<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.staff_members`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.staff`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <StaffSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes(`${userType}_create`) ||
                                    permsArray.includes('admin')
                                "
                            >
                                <a-space>
                                    <a-button type="primary" @click="addItem">
                                        <PlusOutlined />
                                        {{ $t(`${langKey}.add`) }}
                                    </a-button>
                                    <ImportUsers
                                        :pageTitle="importPageTitle"
                                        :sampleFileUrl="sampleFileUrl"
                                        :importUrl="importUrl"
                                        @onUploadSuccess="setUrlData"
                                    />
                                </a-space>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes(`${userType}_delete`) ||
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
                            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                                <a-input-group compact>
                                    <a-input-search
                                        style="width: 100%"
                                        v-model:value="table.searchString"
                                        show-search
                                        @change="onTableSearch"
                                        @search="onTableSearch"
                                        :loading="table.filterLoading"
                                        :placeholder="
                                            $t('common.placeholder_search_text', [
                                                $t('common.name'),
                                            ])
                                        "
                                    />
                                </a-input-group>
                            </a-col>
                            <a-col
                                v-if="userType && userType != 'users'"
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="6"
                                :xl="6"
                            >
                                <a-select
                                    v-model:value="searchStatus"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.status'),
                                        ])
                                    "
                                    :allowClear="true"
                                    optionFilterProp="label"
                                >
                                    <a-select-option
                                        v-for="usrStatus in userStatus"
                                        :key="usrStatus.key"
                                        :value="usrStatus.key"
                                        :label="usrStatus.value"
                                    >
                                        {{ usrStatus.value }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                                <a-select
                                    v-model:value="filters.shift_id"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.shift_id'),
                                        ])
                                    "
                                    :allowClear="true"
                                    style="width: 100%"
                                    optionFilterProp="title"
                                    show-search
                                    @change="setUrlData"
                                >
                                    <a-select-option
                                        v-for="shift in shifts"
                                        :key="shift.xid"
                                        :title="shift.name"
                                        :value="shift.xid"
                                    >
                                        {{ shift.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                                <a-select
                                    v-model:value="filters.department_id"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.department_id'),
                                        ])
                                    "
                                    :allowClear="true"
                                    style="width: 100%"
                                    optionFilterProp="title"
                                    show-search
                                    @change="setUrlData"
                                >
                                    <a-select-option
                                        v-for="department in departments"
                                        :key="department.xid"
                                        :title="department.name"
                                        :value="department.xid"
                                    >
                                        {{ department.name }}
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                                <a-select
                                    v-model:value="filters.designation_id"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.designation_id'),
                                        ])
                                    "
                                    :allowClear="true"
                                    style="width: 100%"
                                    optionFilterProp="title"
                                    show-search
                                    @change="setUrlData"
                                >
                                    <a-select-option
                                        v-for="designation in designations"
                                        :key="designation.xid"
                                        :title="designation.name"
                                        :value="designation.xid"
                                    >
                                        {{ designation.name }}
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
                    :addEditData="formData"
                    :data="viewData"
                    :pageTitle="pageTitle"
                    :successMessage="successMessage"
                />

                <a-row v-if="userType && userType == 'users'">
                    <a-col :span="24">
                        <a-tabs v-model:activeKey="searchStatus" @change="setUrlData">
                            <a-tab-pane
                                key="all"
                                :tab="`${$t('common.all')} ${$t('menu.users')}`"
                            />
                            <a-tab-pane
                                v-for="usrStatus in userStatus"
                                :key="usrStatus.key"
                                :tab="`${usrStatus.value} ${$t('menu.users')}`"
                            />
                        </a-tabs>
                    </a-col>
                </a-row>

                <a-row v-if="userType && userType != 'users'">
                    <a-col :span="24">
                        <a-tabs v-model:activeKey="searchDueType" @change="setUrlData">
                            <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                            <a-tab-pane key="receive" :tab="`${$t('user.to_receive')}`" />
                            <a-tab-pane key="pay" :tab="`${$t('user.to_pay')}`" />
                        </a-tabs>
                    </a-col>
                </a-row>

                <a-row :gutter="[15, 15]">
                    <a-col :span="24">
                        <div class="table-responsive">
                            <a-table
                                :row-selection="{
                                    selectedRowKeys: table.selectedRowKeys,
                                    onChange: onRowSelectChange,
                                    getCheckboxProps: (record) => ({
                                        disabled:
                                            user.xid == record.xid ||
                                            record.is_walkin_customer
                                                ? true
                                                : false,
                                        name: record.xid,
                                    }),
                                }"
                                :columns="tableColumns"
                                :row-key="(record) => record.xid"
                                :data-source="table.data"
                                :pagination="table.pagination"
                                :loading="table.loading"
                                @change="handleTableChange"
                                bordered
                                size="middle"
                            >
                                <template #bodyCell="{ column, text, record }">
                                    <template v-if="column.dataIndex === 'name'">
                                        <user-info :user="record" />
                                    </template>
                                    <template v-if="column.dataIndex === 'status'">
                                        <a-tag :color="statusColors[text]">
                                            {{ $t(`common.${text}`) }}
                                        </a-tag>
                                    </template>
                                    <template v-if="column.dataIndex == 'department_id'">
                                        {{ record.department?.name }}
                                    </template>
                                    <template v-if="column.dataIndex == 'designation_id'">
                                        {{ record.designation?.name }}
                                    </template>
                                    <template v-if="column.dataIndex == 'shift_id'">
                                        {{ record.shift?.name }}
                                    </template>
                                    <template v-if="column.dataIndex === 'created_at'">
                                        {{ formatDateTime(record.created_at) }}
                                    </template>
                                    <template v-if="column.dataIndex === 'balance'">
                                        <UserBalance
                                            :amount="record.details?.due_amount"
                                        />
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-button
                                            type="primary"
                                            @click="viewItem(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><EyeOutlined /></template>
                                        </a-button>
                                        <a-button
                                            v-if="
                                                permsArray.includes(`${userType}_edit`) ||
                                                permsArray.includes('admin')
                                            "
                                            type="primary"
                                            @click="editItem(record)"
                                            style="margin-left: 4px"
                                        >
                                            <template #icon><EditOutlined /></template>
                                        </a-button>
                                        <a-button
                                            v-if="
                                                (permsArray.includes(
                                                    `${userType}_delete`
                                                ) ||
                                                    permsArray.includes('admin')) &&
                                                user.xid != record.xid &&
                                                !record.is_walkin_customer
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

                <UserView
                    :user="viewData"
                    :visible="detailsVisible"
                    @closed="onCloseDetails"
                />
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { computed, onMounted, ref, watch } from "vue";
import {
    EyeOutlined,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    UserOutlined,
    ArrowUpOutlined,
    ArrowDownOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import crud from "../../../../../common/composable/crud";
import common from "../../../../../common/composable/common";
import fields from "./fields";
import UserInfo from "../../../../../common/components/user/UserInfo.vue";
import StateWidget from "../../../../../common/components/common/card/StateWidget.vue";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import UserBalance from "./UserBalance.vue";
import UserView from "./View.vue";
import ImportUsers from "../../../../../common/core/ui/Import.vue";
import StaffSidebar from "../StaffSidebar.vue";

export default {
    components: {
        EyeOutlined,
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        UserOutlined,
        ArrowUpOutlined,
        ArrowDownOutlined,
        AddEdit,
        UserInfo,

        StateWidget,
        AdminPageHeader,
        UserBalance,
        UserView,
        ImportUsers,
        StaffSidebar,
    },
    setup() {
        const { t } = useI18n();
        const {
            statusColors,
            userStatus,
            permsArray,
            formatDateTime,
            user,
            selectedWarehouse,
        } = common();
        const {
            staffMemberInitData,
            columns,
            filterableColumns,
            hashableColumns,
        } = fields();
        const crudVariables = crud();
        const route = useRoute();
        const userType = ref("users");
        const departments = ref([]);
        const designations = ref([]);
        const departmentUrl = "departments?fields=id,xid,name";
        const desigantionUrl = "designations?fields=id,xid,name";
        const urlParams =
            "?fields=id,xid,user_type,name,email,profile_image,profile_image_url,is_walkin_customer,phone,address,shipping_address,status,created_at,details{opening_balance,opening_balance_type,credit_period,credit_limit,due_amount,warehouse_id,x_warehouse_id},details:warehouse{id,xid,name},role_id,role{id,xid,name,display_name},warehouse_id,x_warehouse_id,warehouse{xid,name},userWarehouses{user_id,x_user_id,warehouse_id,x_warehouse_id},department_id,x_department_id,department{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},shift_id,x_shift_id,shift{id,xid,name}";

        const searchStatus = ref(undefined);
        const activeTabKey = ref("all");
        const searchDueType = ref("all");
        const tableColumns = ref([]);
        const sampleFileUrl = ref("");
        const importPageTitle = ref("");
        const importUrl = ref("");
        const filters = ref({
            shift_id: undefined,
            department_id: undefined,
            designation_id: undefined,
        });
        const shifts = ref([]);

        onMounted(() => {
            setUrlData();
            crudVariables.table.filterableColumns = filterableColumns;

            setFormData();
            getInitialData();
        });

        const setFormData = () => {
            crudVariables.initData.value = { ...staffMemberInitData };
            crudVariables.formData.value = { ...staffMemberInitData };
            crudVariables.hashableColumns.value = [...hashableColumns];
            crudVariables.langKey.value = "staff_member";
            tableColumns.value = columns;
            sampleFileUrl.value = window.config.staff_member_sample_file;
            importPageTitle.value = t("staff_member.import_staff_members");
            importUrl.value = "users/import";

            crudVariables.restFormData();
        };

        const setUrlData = () => {
            crudVariables.crudUrl.value = userType.value;
            var filterString = "";
            var extraFilters = {};
            if (searchStatus.value != undefined && searchStatus.value != "all") {
                filterString += `status eq "${searchStatus.value}"`;
            }
            if (
                searchDueType.value != undefined &&
                searchDueType.value != "all" &&
                userType.value != "users"
            ) {
                extraFilters.search_due_type = searchDueType.value;
            }
            crudVariables.tableUrl.value = {
                url: `${userType.value}${urlParams}`,
                filterString,
                extraFilters,
                filters,
            };
            crudVariables.fetch({
                page: 1,
            });
        };
        const getInitialData = () => {
            const shiftsPromise = axiosAdmin.get("shifts?limit=10000");
            const departmentsPromise = axiosAdmin.get(departmentUrl);
            const designationsPromise = axiosAdmin.get(desigantionUrl);

            Promise.all([shiftsPromise, departmentsPromise, designationsPromise]).then(
                ([shiftsResponse, departmentsResponse, designationsResponse]) => {
                    shifts.value = shiftsResponse.data;
                    departments.value = departmentsResponse.data;
                    designations.value = designationsResponse.data;
                }
            );
        };

        watch(
            () => route.meta.menuKey,
            (newVal, oldVal) => {
                userType.value = "users";

                searchStatus.value = undefined;
                crudVariables.table.searchColumn = undefined;
                crudVariables.table.searchString = "";
                searchDueType.value = "all";

                setUrlData();
                setFormData();
            }
        );

        watch(selectedWarehouse, (newVal, oldVal) => {
            setUrlData();
        });

        return {
            statusColors,
            userStatus,
            filterableColumns,
            userType,
            tableColumns,
            searchStatus,
            setUrlData,
            searchDueType,
            activeTabKey,

            ...crudVariables,
            permsArray,
            formatDateTime,
            user,
            sampleFileUrl,
            importPageTitle,
            importUrl,
            filters,
            shifts,
            departments,
            designations,
        };
    },
};
</script>
