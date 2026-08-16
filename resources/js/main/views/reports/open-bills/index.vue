<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header title="Open Bills (Overdue)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.reports`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item> Open Bills (Overdue) </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="12" :md="8" :lg="6">
                <a-input-number
                    v-model:value="filters.days"
                    :min="0"
                    style="width: 100%"
                    addon-before="Older than (days)"
                    @change="onDaysChange"
                />
            </a-col>
            <a-col :xs="24" :sm="12" :md="8" :lg="6">
                <a-select
                    v-model:value="filters.type"
                    style="width: 100%"
                    @change="fetchBills"
                >
                    <a-select-option value="all"> All Bills </a-select-option>
                    <a-select-option value="sales">
                        {{ $t("menu.sales") }}
                    </a-select-option>
                    <a-select-option value="purchases">
                        {{ $t("menu.purchases") }}
                    </a-select-option>
                </a-select>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <div class="table-responsive">
            <a-table
                :columns="columns"
                :row-key="(record) => record.xid"
                :data-source="bills"
                :loading="loading"
                :pagination="{ pageSize: 25 }"
                bordered
                size="middle"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'order_type'">
                        {{
                            record.order_type == "purchases"
                                ? $t("menu.purchases")
                                : $t("menu.sales")
                        }}
                    </template>
                    <template v-if="column.dataIndex === 'order_date'">
                        {{ formatDate(record.order_date) }}
                    </template>
                    <template v-if="column.dataIndex === 'days_open'">
                        <a-tag :color="record.days_open >= 60 ? 'red' : 'orange'">
                            {{ record.days_open }}
                        </a-tag>
                    </template>
                    <template v-if="column.dataIndex === 'total'">
                        {{ formatAmountCurrency(record.total) }}
                    </template>
                    <template v-if="column.dataIndex === 'paid_amount'">
                        {{ formatAmountCurrency(record.paid_amount) }}
                    </template>
                    <template v-if="column.dataIndex === 'due_amount'">
                        {{ formatAmountCurrency(record.due_amount) }}
                    </template>
                </template>
                <template #summary>
                    <a-table-summary-row>
                        <a-table-summary-cell :col-span="6">
                            <a-typography-text strong>
                                {{ $t("common.total") }}
                            </a-typography-text>
                        </a-table-summary-cell>
                        <a-table-summary-cell :col-span="1">
                            <a-typography-text strong>
                                {{ formatAmountCurrency(totalDue) }}
                            </a-typography-text>
                        </a-table-summary-cell>
                    </a-table-summary-row>
                </template>
            </a-table>
        </div>
    </admin-page-table-content>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import { debounce } from "lodash-es";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const { formatDate, formatAmountCurrency, selectedWarehouse } = common();

        const bills = ref([]);
        const totalDue = ref(0);
        const loading = ref(false);
        const filters = ref({
            days: 35,
            type: "all",
        });

        const columns = [
            { title: t("stock.invoice_number"), dataIndex: "invoice_number" },
            { title: "Type", dataIndex: "order_type" },
            { title: t("common.party"), dataIndex: "party" },
            { title: t("stock.order_date"), dataIndex: "order_date" },
            { title: "Days Open", dataIndex: "days_open" },
            { title: t("payments.total_amount"), dataIndex: "total" },
            { title: t("payments.paid_amount"), dataIndex: "paid_amount" },
            { title: t("payments.due_amount"), dataIndex: "due_amount" },
        ];

        const fetchBills = () => {
            loading.value = true;
            const params = {
                days: filters.value.days,
            };
            if (filters.value.type && filters.value.type != "all") {
                params.type = filters.value.type;
            }

            axiosAdmin
                .get("reports/open-bills", { params })
                .then((res) => {
                    bills.value = res.data.bills || [];
                    totalDue.value = res.data.total_due || 0;
                    loading.value = false;
                })
                .catch(() => {
                    bills.value = [];
                    totalDue.value = 0;
                    loading.value = false;
                });
        };

        const onDaysChange = debounce(() => {
            fetchBills();
        }, 500);

        onMounted(fetchBills);
        watch(selectedWarehouse, fetchBills);

        return {
            filters,
            columns,
            bills,
            totalDue,
            loading,
            formatDate,
            formatAmountCurrency,
            fetchBills,
            onDaysChange,
        };
    },
};
</script>
