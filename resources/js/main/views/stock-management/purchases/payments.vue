<template>
    <a-modal
        :open="visible"
        :centered="true"
        :title="$t('payments.payment_details')"
        @cancel="onClose"
        width="40%"
        :footer="false"
    >
        <div>
            <div class="order-details">
                <a-row
                    :gutter="16"
                    style="margin-bottom: 12px; justify-content: center"
                >
                    <a-col :span="8"
                        >{{ $t("payments.invoice_number") }}:
                        {{ order.invoice_number }}</a-col
                    >
                    <a-col :span="8"
                        >{{ $t("payments.status") }}:
                        <span>
                            <a-tag
                                v-if="order.payment_status == 'paid'"
                                color="success"
                                >{{ $t(`payments.paid`) }}</a-tag
                            >
                            <a-tag
                                v-else-if="
                                    order.payment_status == 'partially_paid'
                                "
                                color="warning"
                                >{{ $t(`payments.partially_paid`) }}</a-tag
                            >
                        </span>
                    </a-col>
                    <a-col :span="8"
                        >{{ $t("payments.customer") }}:
                        {{ order.user?.name }}</a-col
                    >
                </a-row>
                <a-row>
                    <a-col :span="24">
                        <div class="table-responsive">
                            <a-table
                                :row-key="(record) => record.xid"
                                :columns="orderPaymentsColumns"
                                :data-source="order.order_payments"
                                :pagination="false"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template
                                        v-if="
                                            column.dataIndex ===
                                            'payment_number'
                                        "
                                    >
                                        {{ record.payment.payment_number }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'date'"
                                    >
                                        {{ formatDate(record.payment.date) }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'amount'"
                                    >
                                        {{
                                            formatAmountCurrency(record.amount)
                                        }}
                                    </template>
                                    <template
                                        v-if="
                                            column.dataIndex ===
                                            'payment_mode_id'
                                        "
                                    >
                                        {{ record.payment.payment_mode.name }}
                                    </template>
                                    <!-- <template
                                        v-if="column.dataIndex === 'action'"
                                    >
                                        <a-button
                                            type="primary"
                                            @click="editItem(record)"
                                            style="margin-left: 4px"
                                            v-if="
                                                permsArray.includes(
                                                    'order_payments_edit'
                                                ) ||
                                                permsArray.includes('admin')
                                            "
                                        >
                                            <template #icon
                                                ><EditOutlined
                                            /></template>
                                        </a-button>
                                        <a-button
                                            type="primary"
                                            @click="
                                                showDeleteConfirm(record.xid)
                                            "
                                            style="margin-left: 4px"
                                            v-if="
                                                permsArray.includes(
                                                    'order_payments_delete'
                                                ) ||
                                                permsArray.includes('admin')
                                            "
                                        >
                                            <template #icon
                                                ><DeleteOutlined
                                            /></template>
                                        </a-button>
                                    </template> -->
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                </a-row>
            </div>
        </div>
    </a-modal>
</template>

<script>
import { ref, createVNode, computed } from "vue";
import {
    LeftOutlined,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import fields from "./fields";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";
import OrderStatus from "../../../../common/components/order/OrderStatus.vue";

export default {
    props: ["order", "visible"],
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        LeftOutlined,
        UserInfo,
        PaymentStatus,
        OrderStatus,
    },
    setup(props, { emit }) {
        const { orderPaymentsColumns, orderItemDetailsColumns } = fields();
        const { permsArray, formatDate, formatDateTime, formatAmountCurrency } =
            common();
        const { t } = useI18n();

        const onClose = () => {
            emit("close");
        };

        return {
            onClose,
            formatAmountCurrency,
            formatDate,
            formatDateTime,
            orderPaymentsColumns,
            orderItemDetailsColumns,
            permsArray,
        };
    },
};
</script>

<style lang="less">
.order-details {
    .ant-descriptions-item {
        padding-bottom: 5px;
    }
}
</style>
