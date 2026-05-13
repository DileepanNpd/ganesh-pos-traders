<template>
    <a-row>
        <a-col :span="24">
            <div class="table-responsive">
                <a-table
                    :columns="paymentsColumns"
                    :row-key="(record) => record.xid"
                    :data-source="table.data"
                    :pagination="table.pagination"
                    :loading="table.loading"
                    @change="handleTableChange"
                    id="payment-reports-table"
                    bordered
                    size="middle"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'date'">
                            {{ formatDate(record.date) }}
                        </template>
                        <template v-if="column.dataIndex === 'payment_type'">
                            {{
                                record.payment_type == "in"
                                    ? $t("menu.payment_in")
                                    : $t("menu.payment_out")
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'mode_type'">
                            {{
                                record.payment_mode && record.payment_mode.name
                                    ? record.payment_mode?.name
                                    : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'user_id'">
                            <UserInfo :user="record.user" />
                        </template>
                        <template v-if="column.dataIndex === 'amount'">
                            {{ formatAmountCurrency(record.amount) }}
                        </template>
                    </template>
                    <template #summary>
                        <a-table-summary-row>
                            <a-table-summary-cell :col-span="4"> </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ $t("common.total") }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    <a-tooltip>
                                        {{ formatAmountCurrency(totals.totalAmount) }}
                                    </a-tooltip>
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

export default defineComponent({
    props: {
        user_id: null,
        paymentMode: null,
        payment_type: null,
        dates: {
            default: [],
            type: null,
        },
    },
    components: {
        UserInfo,
    },
    setup(props) {
        const { paymentsColumns, paymentsHashableColumns } = fields();
        const { formatDate, formatAmountCurrency, selectedWarehouse } = common();
        const datatableVariables = datatable();

        onMounted(() => {
            const propsData = props;
            getData(propsData);
        });

        const getData = (propsData) => {
            const filters = {};
        
            if (propsData.user_id) {
                filters.user_id = propsData.user_id;
            }
        
            if (propsData.payment_type) {
                filters.payment_type = propsData.payment_type;
            }
        
            datatableVariables.tableUrl.value = {
                url:
                    "payments?fields=id,xid,date,payment_type,amount,payment_number,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url,user_type},payment_mode_id,x_payment_mode_id,paymentMode{id,xid,name,mode_type}",
                filters,
                extraFilters: {
                    dates: propsData.dates,
                    payment_mode: propsData.paymentMode,
                },
            };
        
            datatableVariables.hashable.value = [...paymentsHashableColumns];
            datatableVariables.table.sorter = { field: "date", order: "desc" };
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "payment_reports",
            };
        
            datatableVariables.fetch({ page: 1 });
        };


        watch(props, (newVal, oldVal) => {
            getData(newVal);
        });

        watch(selectedWarehouse, (newVal, oldVal) => {
            getData(props);
        });
        const totals = computed(() => {
            let totalAmount = 0;
            datatableVariables.table.data.forEach((tableRowData) => {
                totalAmount += tableRowData.amount;
            });
            return {
                totalAmount,
            };
        });

        return {
            paymentsColumns,
            ...datatableVariables,

            formatDate,
            formatAmountCurrency,
            totals,
        };
    },
});
</script>
