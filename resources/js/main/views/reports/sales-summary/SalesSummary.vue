<template>
    <a-row>
        <a-col :span="24">
            <div class="table-responsive">
                <a-table
                    :columns="salesSummaryColumns"
                    :row-key="(record) => record.xid"
                    :data-source="table.data"
                    :pagination="table.pagination"
                    :loading="table.loading"
                    @change="handleTableChange"
                    id="sales-summary-reports-table"
                    bordered
                    size="middle"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'order_date'">
                            {{ formatDateTime(record.order_date) }}
                        </template>
                        <template v-if="column.dataIndex === 'payment_type'">
                            {{
                                record.payment_type == "in"
                                    ? $t("menu.payment_in")
                                    : $t("menu.payment_out")
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'user_id'">
                            <UserInfo :user="record.user" />
                        </template>
                        <template v-if="column.dataIndex === 'amount'">
                            {{ formatAmountCurrency(record.total) }}
                        </template>
                        <template v-if="column.dataIndex === 'returns_amount'">
                            {{ formatAmountCurrency(returnsAmount(record)) }}
                        </template>
                        <template v-if="column.dataIndex === 'net_amount'">
                            {{ formatAmountCurrency(netAmount(record)) }}
                        </template>
                        <template v-if="column.dataIndex === 'payment_status'">
                            <PaymentStatus :paymentStatus="record.payment_status" />
                        </template>
                        <template v-if="column.dataIndex === 'staff_user_id'">
                            {{ record.staff_member.name }}
                        </template>
                    </template>
                    <template #summary>
                        <a-table-summary-row>
                            <a-table-summary-cell :col-span="2"> </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ $t("common.total") }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ formatAmountCurrency(totals.totalAmount) }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ formatAmountCurrency(totals.returnsTotal) }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ formatAmountCurrency(totals.netTotal) }}
                                </a-typography-text>
                            </a-table-summary-cell>
                        </a-table-summary-row>
                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent, ref, onMounted, watch, computed } from "vue";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import fields from "./fields";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";

export default defineComponent({
    props: {
        user_id: null,
        paymentMode: null,
        dates: {
            default: [],
            type: null,
        },
    },
    components: {
        UserInfo,
        PaymentStatus,
    },
    setup(props) {
        const { salesSummaryColumns, orderHashableColumns } = fields();
        const { formatDateTime, formatAmountCurrency, selectedWarehouse } = common();
        const datatableVariables = datatable();

        onMounted(() => {
            const propsData = props;
            getData(propsData);
        });

        const getData = (propsData) => {
            const filters = {};

            if (propsData.user_id && propsData.user_id != undefined) {
                filters.staff_user_id = propsData.user_id;
            }

            datatableVariables.tableUrl.value = {
                url:
                    "sales?fields=id,xid,order_date,invoice_number,total,payment_status,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url,user_type},staff_user_id,x_staff_user_id,staffMember{id,xid,name,profile_image,profile_image_url,user_type},returns{id,xid,total}",
                filters,
                extraFilters: {
                    dates: propsData.dates,
                },
            };
            datatableVariables.hashable.value = [...orderHashableColumns];
            datatableVariables.table.sorter = { field: "order_date", order: "desc" };
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "sales_summary_reports",
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        const returnsAmount = (record) => {
            return (record.returns || []).reduce(
                (sum, r) => sum + (parseFloat(r.total) || 0),
                0
            );
        };
        const netAmount = (record) => {
            return (parseFloat(record.total) || 0) - returnsAmount(record);
        };

        const totals = computed(() => {
            let totalAmount = 0;
            let returnsTotal = 0;
            datatableVariables.table.data.forEach((tableRowData) => {
                totalAmount += parseFloat(tableRowData.total) || 0;
                returnsTotal += returnsAmount(tableRowData);
            });
            return {
                totalAmount,
                returnsTotal,
                netTotal: totalAmount - returnsTotal,
            };
        });

        watch(props, (newVal, oldVal) => {
            getData(newVal);
        });

        watch(selectedWarehouse, (newVal, oldVal) => {
            getData(props);
        });

        return {
            salesSummaryColumns,
            ...datatableVariables,

            formatDateTime,
            formatAmountCurrency,
            totals,
            returnsAmount,
            netAmount,
        };
    },
});
</script>
