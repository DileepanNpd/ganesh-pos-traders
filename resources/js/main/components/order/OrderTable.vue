<template>
    <a-row>
        <a-col :span="24">
            <div class="table-responsive">
                <a-table
                    :row-selection="
                        selectable && orderType != 'online-orders'
                            ? {
                                  selectedRowKeys: table.selectedRowKeys,
                                  onChange: (rowKeys, rowValues) => {
                                      onRowSelectChange(rowKeys);
                                      $emit('onRowSelection', rowKeys);
                                  },
                                  getCheckboxProps: getCheckboxProps,
                              }
                            : null
                    "
                    :columns="columns"
                    :row-key="(record) => record.xid"
                    :data-source="table.data"
                    :pagination="table.pagination"
                    :loading="table.loading"
                    @change="handleTableChange"
                    :bordered="bordered"
                    :size="tableSize"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'invoice_number'">
                            <a-typography-link @click="viewItem(record)">
                                {{ record.invoice_number }}
                            </a-typography-link>
                        </template>
                        <template v-if="column.dataIndex === 'order_date'">
                            {{ formatDate(record.order_date) }}
                        </template>
                        <template v-if="column.dataIndex === 'warehouse'">
                            <span
                                v-if="record.warehouse && record.warehouse.xid"
                            >
                                {{ record.warehouse.name }}
                            </span>
                        </template>
                        <a-typography-link @click="userView(record)">
                            <template v-if="column.dataIndex === 'user_id'">
                                <user-info :user="record.user" />
                            </template>
                        </a-typography-link>
                        <template v-if="column.dataIndex === 'paid_amount'">
                            {{ formatAmountCurrency(record.paid_amount) }}
                        </template>
                        <template v-if="column.dataIndex === 'total_amount'">
                            {{ formatAmountCurrency(record.total) }}
                        </template>
                        <template v-if="column.dataIndex === 'due_amount'">
                            {{ formatAmountCurrency(record.due_amount) }}
                        </template>
                        <template v-if="column.dataIndex === 'payment_status'">
                            <PaymentStatus
                                :paymentStatus="record.payment_status"
                            />
                        </template>
                        <template v-if="column.dataIndex === 'order_status'">
                            <OrderStatus :data="record" />
                        </template>
                        <template v-if="column.dataIndex === 'action'">
                            <a-space
                                v-if="
                                    record.order_type == 'online-orders' &&
                                    record.order_status != 'delivered' &&
                                    !record.cancelled
                                "
                            >
                                <a-tooltip
                                    placement="topLeft"
                                    :title="$t('stock.view_order')"
                                >
                                    <a-button
                                        type="primary"
                                        @click="viewOrder(record)"
                                    >
                                        <template #icon>
                                            <EyeOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                                <a-tooltip
                                    v-if="
                                        record.order_status == 'ordered' &&
                                        !record.cancelled
                                    "
                                    placement="topLeft"
                                    :title="$t('common.confirm')"
                                >
                                    <a-button
                                        type="primary"
                                        @click="confirmOrder(record)"
                                    >
                                        <template #icon>
                                            <CheckOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                                <a-tooltip
                                    v-if="
                                        !record.cancelled &&
                                        record.order_status != 'ordered' &&
                                        record.order_status != 'delivered'
                                    "
                                    placement="topLeft"
                                    :title="
                                        $t('online_orders.confirm_delivery')
                                    "
                                >
                                    <a-button
                                        type="primary"
                                        @click="confirmDelivery(record)"
                                    >
                                        <template #icon>
                                            <SendOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                                <a-tooltip
                                    v-if="
                                        !record.cancelled &&
                                        record.order_status != 'delivered'
                                    "
                                    placement="topLeft"
                                    :title="$t('common.cancel')"
                                >
                                    <a-button
                                        type="primary"
                                        @click="cancelOrder(record)"
                                        danger
                                    >
                                        <template #icon>
                                            <StopOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                            </a-space>
                            <a-dropdown
                                v-else-if="
                                    record.order_type == 'stock-transfers'
                                "
                                placement="bottomRight"
                            >
                                <MoreOutlined />
                                <template #overlay>
                                    <a-menu>
                                        <a-menu-item
                                            key="view"
                                            v-if="
                                                permsArray.includes(
                                                    `${pageObject.permission}_view`
                                                ) ||
                                                permsArray.includes('admin')
                                            "
                                            @click="viewItem(record)"
                                        >
                                            <EyeOutlined />
                                            {{ $t("common.view") }}
                                        </a-menu-item>
                                        <a-menu-item
                                            key="edit"
                                            v-if="
                                                filters.transfer_type ==
                                                    'transfered' &&
                                                (permsArray.includes(
                                                    `${pageObject.permission}_edit`
                                                ) ||
                                                    permsArray.includes(
                                                        'admin'
                                                    ))
                                            "
                                            @click="
                                                () =>
                                                    $router.push({
                                                        name: `admin.stock.${pageObject.type}.edit`,
                                                        params: {
                                                            id: record.xid,
                                                        },
                                                    })
                                            "
                                        >
                                            <EditOutlined />
                                            {{ $t("common.edit") }}
                                        </a-menu-item>
                                        <a-menu-item
                                            key="delete"
                                            v-if="
                                                filters.transfer_type ==
                                                    'transfered' &&
                                                (permsArray.includes(
                                                    `${pageObject.permission}_delete`
                                                ) ||
                                                    permsArray.includes(
                                                        'admin'
                                                    )) &&
                                                record.payment_status ==
                                                    'unpaid'
                                            "
                                            @click="
                                                showDeleteConfirm(record.xid)
                                            "
                                        >
                                            <DeleteOutlined />
                                            {{ $t("common.delete") }}
                                        </a-menu-item>

                                        <a-menu-divider />

                                        <a-menu-item
                                            key="view_payments"
                                            v-if="
                                              (  permsArray.includes(
                                                    `${pageObject.permission}_view`
                                                ) ||
                                                permsArray.includes('admin')) ||
                                                record.payment_status !=
                                                    'unpaid'
                                            "
                                            @click="viewPaymentDetails(record)"
                                        >
                                            <WalletOutlined />
                                            {{ $t("payments.view_payments") }}
                                        </a-menu-item>

                                        <a-menu-item
                                            key="payments"
                                            v-if="
                                                (permsArray.includes(
                                                    'order_payments_view'
                                                ) ||
                                                permsArray.includes('admin')
                                                    ? 'payments'
                                                    : 'order_items') &&
                                                record.payment_status != 'paid'
                                            "
                                            @click="addItem(record)"
                                        >
                                            <DollarCircleOutlined />
                                            {{ $t("payments.add") }}
                                        </a-menu-item>

                                        <a-menu-item key="download_invoice">
                                            <a-typography-link
                                                :href="`${invoiceBaseUrl}/${record.unique_id}/${selectedLang}`"
                                                target="_blank"
                                            >
                                                <DownloadOutlined />
                                                {{
                                                    $t(
                                                        "common.download_invoice"
                                                    )
                                                }}
                                            </a-typography-link>
                                        </a-menu-item>

                                        <a-menu-divider />

                                        <a-menu-item
                                            key="print_pdf_invoice"
                                            @click="
                                                printInvoicePDF(
                                                    `${invoiceBaseUrl}/${record.unique_id}/${selectedLang}`
                                                )
                                            "
                                        >
                                            <PrinterOutlined />
                                            {{ $t("common.print_invoice") }}
                                        </a-menu-item>
                                    </a-menu>
                                </template>
                            </a-dropdown>
                            <a-dropdown v-else placement="bottomRight">
                                <MoreOutlined />
                                <template #overlay>
                                    <a-menu>
                                        <a-menu-item
                                            key="view"
                                            v-if="
                                                (permsArray.includes(
                                                    `${pageObject.permission}_view`
                                                ) ||
                                                    permsArray.includes(
                                                        'admin'
                                                    )) &&
                                                record.order_type ==
                                                    'quotations'
                                            "
                                            @click="convertToSale(record)"
                                        >
                                            <SisternodeOutlined />
                                            {{
                                                $t("quotation.convert_to_sale")
                                            }}
                                        </a-menu-item>
                                        <a-menu-item
                                            key="view"
                                            v-if="
                                                permsArray.includes(
                                                    `${pageObject.permission}_view`
                                                ) ||
                                                permsArray.includes('admin')
                                            "
                                            @click="viewItem(record)"
                                        >
                                            <EyeOutlined />
                                            {{ $t("common.view") }}
                                        </a-menu-item>
                                        <a-menu-item
                                            key="edit"
                                            v-if="
                                                record.order_type !=
                                                    'online-orders' &&
                                                (permsArray.includes(
                                                    `${pageObject.permission}_edit`
                                                ) ||
                                                    permsArray.includes(
                                                        'admin'
                                                    ))
                                            "
                                            @click="
                                                () =>
                                                    $router.push({
                                                        name: `admin.stock.${pageObject.type}.edit`,
                                                        params: {
                                                            id: record.xid,
                                                        },
                                                    })
                                            "
                                        >
                                            <EditOutlined />
                                            {{ $t("common.edit") }}
                                        </a-menu-item>
                                        <a-menu-item
                                            key="delete"
                                            v-if="
                                                record.order_type !=
                                                    'online-orders' &&
                                                (permsArray.includes(
                                                    `${pageObject.permission}_delete`
                                                ) ||
                                                    permsArray.includes(
                                                        'admin'
                                                    )) &&
                                                record.payment_status ==
                                                    'unpaid'
                                            "
                                            @click="
                                                showDeleteConfirm(record.xid)
                                            "
                                        >
                                            <DeleteOutlined />
                                            {{ $t("common.delete") }}
                                        </a-menu-item>

                                        <a-menu-divider />

                                        <a-menu-item
                                            key="view_payments"
                                            v-if="
                                                permsArray.includes(
                                                    `${pageObject.permission}_view`
                                                ) ||
                                                permsArray.includes('admin')
                                            "
                                            @click="viewPaymentDetails(record)"
                                        >
                                            <WalletOutlined />
                                            {{ $t("payments.view_payments") }}
                                        </a-menu-item>
                                        <a-menu-item
                                            key="payments"
                                            v-if="
                                                (permsArray.includes(
                                                    'order_payments_view'
                                                ) ||
                                                permsArray.includes('admin')
                                                    ? 'payments'
                                                    : 'order_items') &&
                                                record.payment_status != 'paid'
                                            "
                                            @click="addItem(record)"
                                        >
                                            <DollarCircleOutlined />
                                            {{ $t("payments.add") }}
                                        </a-menu-item>
                                        <a-menu-item
                                            key="pos_invoice"
                                            v-if="
                                                record.order_type == 'sales' &&
                                                (permsArray.includes(
                                                    `${pageObject.permission}_edit`
                                                ) ||
                                                    permsArray.includes(
                                                        'admin'
                                                    ))
                                            "
                                            @click="posView(record)"
                                        >
                                            <ShoppingCartOutlined />
                                            {{ $t("common.pos_invoice") }}
                                        </a-menu-item>

                                        <a-menu-item key="download_invoice">
                                            <a-typography-link
                                                :href="`${invoiceBaseUrl}/${record.unique_id}/${selectedLang}`"
                                                target="_blank"
                                            >
                                                <DownloadOutlined />
                                                {{
                                                    $t(
                                                        "common.download_invoice"
                                                    )
                                                }}
                                            </a-typography-link>
                                        </a-menu-item>

                                        <a-menu-divider />

                                        <a-menu-item
                                            key="print_pdf_invoice"
                                            @click="
                                                printInvoicePDF(
                                                    `${invoiceBaseUrl}/${record.unique_id}/${selectedLang}`
                                                )
                                            "
                                        >
                                            <PrinterOutlined />
                                            {{ $t("common.print_invoice") }}
                                        </a-menu-item>
                                    </a-menu>
                                </template>
                            </a-dropdown>
                        </template>
                    </template>
                    <template #expandedRowRender="orderItemData">
                        <a-table
                            v-if="
                                orderItemData &&
                                orderItemData.record &&
                                orderItemData.record.items
                            "
                            :row-key="(record) => record.xid"
                            :columns="orderItemDetailsColumns"
                            :data-source="orderItemData.record.items"
                            :pagination="false"
                        >
                            <template #bodyCell="{ column, record }">
                                <template
                                    v-if="column.dataIndex === 'product_id'"
                                >
                                    <a-badge>
                                        <a-avatar
                                            shape="square"
                                            :src="record.product.image_url"
                                        />
                                        {{ record.product.name }}
                                    </a-badge>
                                </template>
                                <template
                                    v-if="column.dataIndex === 'quantity'"
                                >
                                    {{
                                        `${record.quantity} ${record.product.unit.short_name}`
                                    }}
                                </template>
                                <template
                                    v-if="
                                        column.dataIndex === 'single_unit_price'
                                    "
                                >
                                    {{
                                        formatAmountCurrency(
                                            record.single_unit_price
                                        )
                                    }}
                                </template>
                                <template
                                    v-if="column.dataIndex === 'total_discount'"
                                >
                                    {{
                                        formatAmountCurrency(
                                            record.total_discount
                                        )
                                    }}
                                </template>
                                <template
                                    v-if="column.dataIndex === 'total_tax'"
                                >
                                    <span
                                        v-if="
                                            record.order_item_taxes.length > 0
                                        "
                                    >
                                        <span
                                            v-for="order_item_tax in record.order_item_taxes"
                                            :key="order_item_tax.xid"
                                        >
                                            <span>
                                                {{ order_item_tax.tax_name }} :
                                                {{
                                                    formatAmountCurrency(
                                                        order_item_tax.tax_amount
                                                    )
                                                }}
                                            </span>
                                            <br />
                                        </span>
                                    </span>
                                    <span v-else>
                                        {{
                                            formatAmountCurrency(
                                                record.total_tax
                                            )
                                        }}
                                    </span>
                                </template>
                                <template
                                    v-if="column.dataIndex === 'subtotal'"
                                >
                                    {{ formatAmountCurrency(record.subtotal) }}
                                </template>
                            </template>
                        </a-table>
                    </template>
                    <template #summary>
                        <a-table-summary-row>
                            <a-table-summary-cell
                                :col-span="
                                    selectable && orderType != 'online-orders'
                                        ? 5
                                        : 4
                                "
                            >
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ $t("common.total") }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{
                                        formatAmountCurrency(totals.totalAmount)
                                    }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{
                                        formatAmountCurrency(totals.paidAmount)
                                    }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ formatAmountCurrency(totals.dueAmount) }}
                                </a-typography-text>
                            </a-table-summary-cell>
                        </a-table-summary-row>
                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>

    <OrderDetails
        :visible="detailsDrawerVisible"
        :order="selectedItem"
        @close="onDetailDrawerClose"
        @goBack="restSelectedItem"
        @reloadOrder="paymentSuccess"
    />

    <Payments
        :visible="paymentModalVisible"
        :order="selectedItem"
        @close="closePaymentDetails"
    />

    <ConfirmOrder
        :visible="confirmModalVisible"
        :data="modalData"
        @closed="confirmModalVisible = false"
        @confirmSuccess="initialSetup"
    />

    <ViewOrder
        :visible="viewModalVisible"
        :order="modalData"
        @closed="viewModalVisible = false"
    />

    <AddEdit
        :addEditType="addEditType"
        :visible="addEditVisible"
        :url="addEditUrl"
        @addEditSuccess="addEditSuccess"
        @closed="onCloseAddEdit"
        :formData="formData"
        :data="selectedItem"
        :editItemAmount="editItemAmount"
        :pageTitle="pageTitle"
        :successMessage="successMessage"
    />
    <InvoiceVue
        :visible="printInvoiceModalVisible"
        :order="printInvoiceOrder"
        @closed="printInvoiceModalVisible = false"
    />
    <View
        :visible="userVisible"
        :user="modalData"
        @closed="userVisible = false"
    />
</template>

<script>
import { onMounted, watch, ref, createVNode, computed } from "vue";
import {
    EyeOutlined,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
    MoreOutlined,
    DownloadOutlined,
    CheckOutlined,
    StopOutlined,
    SendOutlined,
    SisternodeOutlined,
    DollarCircleOutlined,
    WalletOutlined,
    ShoppingCartOutlined,
    PrinterOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import { find, forEach } from "lodash-es";
import { useI18n } from "vue-i18n";
import print from "print-js";
import fields from "../../views/stock-management/purchases/fields";
import common from "../../../common/composable/common";
import datatable from "../../../common/composable/datatable";
import PaymentStatus from "../../../common/components/order/PaymentStatus.vue";
import OrderStatus from "../../../common/components/order/OrderStatus.vue";
import Details from "../../views/stock-management/purchases/Details.vue";
import UserInfo from "../../../common/components/user/UserInfo.vue";
import OrderDetails from "./OrderDetails.vue";
import ConfirmOrder from "../../views/stock-management/online-orders/ConfirmOrder.vue";
import ViewOrder from "../../views/stock-management/online-orders/ViewOrder.vue";
import AddEdit from "../../views/stock-management/order-payments/AddEdit.vue";
import InvoiceVue from "../../views/stock-management/pos/Invoice.vue";
import Payments from "../../views/stock-management/purchases/payments.vue";
import View from "../../views/users/View.vue";

export default {
    props: {
        selectable: {
            default: false,
        },
        tableSize: {
            default: "large",
        },
        bordered: {
            default: false,
        },
        orderType: {
            default: "",
        },
        filters: {
            default: {},
        },
        perPageItems: Number,
    },
    emits: ["onRowSelection"],
    components: {
        EyeOutlined,
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        MoreOutlined,
        DownloadOutlined,
        ExclamationCircleOutlined,
        SisternodeOutlined,
        CheckOutlined,
        StopOutlined,
        SendOutlined,
        WalletOutlined,
        ShoppingCartOutlined,
        PrinterOutlined,
        Details,
        UserInfo,
        Details,
        PaymentStatus,
        OrderStatus,
        OrderDetails,
        ConfirmOrder,
        ViewOrder,
        AddEdit,
        DollarCircleOutlined,
        InvoiceVue,
        Payments,
        View,
    },
    setup(props, { emit }) {
        const store = useStore();
        const {
            columns,
            hashableColumns,
            setupTableColumns,
            filterableColumns,
            pageObject,
            orderType,
            orderStatus,
            orderItemDetailsColumns,
            initPaymentData,
        } = fields();
        const datatableVariables = datatable();
        const {
            formatAmountCurrency,
            invoiceBaseUrl,
            permsArray,
            calculateOrderFilterString,
            formatDate,
            selectedWarehouse,
            selectedLang,
            orderStatusColors,
        } = common();
        const route = useRoute();
        const { t } = useI18n();
        const detailsDrawerVisible = ref(false);

        const selectedItem = ref({});
        const printInvoiceModalVisible = ref(false);
        const payNowVisible = ref(false);
        const printInvoiceOrder = ref({});

        // For Online Orders
        const confirmModalVisible = ref(false);
        const viewModalVisible = ref(false);
        const paymentModalVisible = ref(false);
        const userVisible = ref(false);
        const modalData = ref({});
        // End For Online Orders

        const posView = (order) => {
            var totalMrp = 0;
            var totalTax = 0;
            forEach(order.items, (item) => {
                totalMrp += item.quantity * item.mrp;
                totalTax += item.total_tax;
            });

            let savingOnMrp = totalMrp - order.total;
            let savingPercentage =
                totalMrp > 0 ? ((savingOnMrp / totalMrp) * 100).toFixed(2) : 0;
            let totalTaxOnItems = totalTax + order.tax_amount;

            printInvoiceOrder.value = {
                ...order,
                saving_on_mrp: savingOnMrp,
                saving_percentage: savingPercentage,
                total_tax_on_items: totalTaxOnItems,
            };
            printInvoiceModalVisible.value = true;
        };

        onMounted(() => {
            initialSetup();
        });

        const getCheckboxProps = (record) => {
            var isDeleteable = false;

            if (
                record.order_type == "stock-transfers" &&
                props.filters.transfer_type == "transfered" &&
                (permsArray.value.includes(
                    `${pageObject.value.permission}_delete`
                ) ||
                    permsArray.value.includes("admin")) &&
                record.payment_status == "unpaid"
            ) {
                isDeleteable = true;
            } else if (
                record.order_type != "online-orders" &&
                (permsArray.value.includes(
                    `${pageObject.value.permission}_delete`
                ) ||
                    permsArray.value.includes("admin")) &&
                record.payment_status == "unpaid"
            ) {
                isDeleteable = true;
            }

            return {
                disabled: !isDeleteable,
                name: record.xid,
            };
        };

        const initialSetup = () => {
            orderType.value = props.orderType;
            if (props.perPageItems) {
                datatableVariables.table.pagination.pageSize =
                    props.perPageItems;
            }
            datatableVariables.table.pagination.current = 1;
            datatableVariables.table.pagination.currentPage = 1;
            datatableVariables.hashable.value = hashableColumns;

            setupTableColumns();
            setUrlData();
        };

        const setUrlData = () => {
            const tableFilter = props.filters;

            const filterString = calculateOrderFilterString(tableFilter);

            var extraFilterObject = {};
            if (tableFilter.dates) {
                extraFilterObject.dates = tableFilter.dates;
            }
            if (tableFilter.transfer_type) {
                extraFilterObject.transfer_type = tableFilter.transfer_type;
            }

            datatableVariables.tableUrl.value = {
                url: `${props.orderType}?fields=id,total_items,total_quantity,xid,unique_id,warehouse_id,x_warehouse_id,warehouse{id,xid,name},from_warehouse_id,x_from_warehouse_id,fromWarehouse{id,xid,name},invoice_number,order_type,order_date,tax_amount,discount,shipping,subtotal,paid_amount,due_amount,order_status,payment_status,total,tax_rate,staff_user_id,x_staff_user_id,staffMember{id,xid,name,profile_image,profile_image_url,shipping_address,tax_number,email,user_type},user_id,x_user_id,user{id,xid,user_type,name,email,address,tax_number,profile_image,profile_image_url,phone},user:details{opening_balance,opening_balance_type,credit_period,credit_limit,due_amount,warehouse_id,x_warehouse_id},orderPayments{id,xid,amount,payment_id,x_payment_id},orderPayments:payment{id,xid,payment_number,amount,payment_mode_id,x_payment_mode_id,date,notes},orderPayments:payment:paymentMode{id,xid,name},items{id,xid,product_id,x_product_id,unit_id,x_unit_id,single_unit_price,unit_price,quantity,tax_rate,total_tax,tax_type,total_discount,subtotal,mrp},items:unit{id,xid,name,short_name},items:product{id,xid,name,image,image_url},items:product:unit{id,xid,name,short_name},items:product:details{id,xid,warehouse_id,x_warehouse_id,product_id,x_product_id,current_stock},items:orderItemTaxes{id,xid,order_item_id,order_item_id,tax_name,tax_amount},cancelled,terms_condition,shippingAddress{id,xid,order_id,name,email,phone,address,address,city,state,country,zipcode},items:product:customFields{id,xid,field_name,field_value,warehouse_id}`,
                filterString,
                filters: {
                    user_id: tableFilter.user_id
                        ? tableFilter.user_id
                        : undefined,
                    warehouse_id: tableFilter.warehouse_id
                        ? tableFilter.warehouse_id
                        : undefined,
                },
                extraFilters: extraFilterObject,
            };
            datatableVariables.table.filterableColumns = filterableColumns;

            if (
                tableFilter.searchColumn &&
                tableFilter.searchString &&
                tableFilter.searchString != ""
            ) {
                datatableVariables.table.searchColumn =
                    tableFilter.searchColumn;
                datatableVariables.table.searchString =
                    tableFilter.searchString;
            } else {
                datatableVariables.table.searchColumn = undefined;
                datatableVariables.table.searchString = "";
            }

            datatableVariables.fetch({
                page: datatableVariables.table.pagination.currentPage,
            });
        };

        const showDeleteConfirm = (id) => {
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t(`${pageObject.value.langKey}.delete_message`),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    axiosAdmin.delete(`${props.orderType}/${id}`).then(() => {
                        // Update Visible Subscription Modules
                        updateSubscriptionModules();
                        setUrlData();

                        notification.success({
                            message: t("common.success"),
                            description: t(
                                `${pageObject.value.langKey}.deleted`
                            ),
                        });
                    });
                },
                onCancel() {},
            });
        };

        const showSelectedDeleteConfirm = () => {
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t(
                    `${pageObject.value.langKey}.selected_delete_message`
                ),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    const allDeletePromise = [];
                    forEach(
                        datatableVariables.table.selectedRowKeys,
                        (selectedRow) => {
                            allDeletePromise.push(
                                axiosAdmin.delete(
                                    `${props.orderType}/${selectedRow}`
                                )
                            );
                        }
                    );

                    Promise.all(allDeletePromise).then((successResponse) => {
                        // Update Visible Subscription Modules
                        updateSubscriptionModules();

                        resetSelectedRows();
                        setUrlData();

                        notification.success({
                            message: t("common.success"),
                            description: t(
                                `${pageObject.value.langKey}.deleted`
                            ),
                            placement: "bottomRight",
                        });
                    });
                },
                onCancel() {},
            });
        };

        // Update Visible Subscription Modules
        const updateSubscriptionModules = () => {
            store.dispatch("auth/updateVisibleSubscriptionModules");
        };

        const viewItem = (record) => {
            selectedItem.value = record;
            detailsDrawerVisible.value = true;
        };
        const userView = (record) => {
            let user = {};
            user = record.user;
            modalData.value = { ...record, ...user };
            userVisible.value = true;
        };

        const viewPaymentDetails = (record) => {
            selectedItem.value = record;
            paymentModalVisible.value = true;
        };
        const closePaymentDetails = () => {
            paymentModalVisible.value = false;
        };

        const restSelectedItem = () => {
            selectedItem.value = {};
        };

        const paymentSuccess = () => {
            datatableVariables.fetch({
                page: datatableVariables.currentPage.value,
                success: (results) => {
                    const searchResult = find(results, (result) => {
                        return result.xid == selectedItem.value.xid;
                    });

                    if (searchResult != undefined) {
                        selectedItem.value = searchResult;
                    }
                },
            });
        };

        const onDetailDrawerClose = () => {
            detailsDrawerVisible.value = false;
        };

        // For Online Orders
        const confirmOrder = (order) => {
            modalData.value = order;
            confirmModalVisible.value = true;
        };

        const viewOrder = (order) => {
            modalData.value = order;
            viewModalVisible.value = true;
        };

        const changeOrderStatus = (order) => {
            processRequest({
                url: `online-orders/change-status/${order.unique_id}`,
                data: { order_status: order.order_status },
                success: (res) => {
                    // Toastr Notificaiton
                    notification.success({
                        placement: "bottomRight",
                        message: t("common.success"),
                        description: t("online_orders.order_status_changed"),
                    });
                },
                error: (errorRules) => {},
            });
        };

        const convertToSale = (order) => {
            Modal.confirm({
                title: t("quotation.convert_to_sale") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t(`quotation.convert_message`),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    axiosAdmin
                        .post(`quotations/convert-to-sale/${order.unique_id}`)
                        .then(() => {
                            datatableVariables.fetch();

                            // Toastr Notificaiton
                            notification.success({
                                placement: "bottomRight",
                                message: t("common.success"),
                                description: t(
                                    "quotation.quotation_converted_to_sales"
                                ),
                            });
                        });
                },
                onCancel() {},
            });
        };

        const cancelOrder = (order) => {
            Modal.confirm({
                title: t("online_orders.cancel_order") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t(`online_orders.cancel_message`),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    axiosAdmin
                        .post(`online-orders/cancel/${order.unique_id}`)
                        .then(() => {
                            initialSetup();
                            notification.success({
                                message: t("common.success"),
                                description: t(`online_orders.order_cancelled`),
                                placement: "bottomRight",
                            });
                        });
                },
                onCancel() {},
            });
        };

        const confirmDelivery = (order) => {
            Modal.confirm({
                title: t("common.delivered") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t(`online_orders.deliver_message`),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    axiosAdmin
                        .post(`online-orders/delivered/${order.unique_id}`)
                        .then(() => {
                            initialSetup();
                            notification.success({
                                message: t("common.success"),
                                description: t(`online_orders.order_delivered`),
                                placement: "bottomRight",
                            });
                        });
                },
                onCancel() {},
            });
        };
        // End For Online Orders

        const resetSelectedRows = () => {
            datatableVariables.table.selectedRowKeys = [];
            emit("onRowSelection", []);
        };

        const printInvoicePDF = async (pdfUrl) => {
            datatableVariables.table.loading = true;

            await axiosAdmin
                .get(pdfUrl, {
                    responseType: "blob",
                })
                .then((response) => {
                    const pdfBlob = response;

                    const url = URL.createObjectURL(pdfBlob);

                    print(url);

                    URL.revokeObjectURL(url);

                    datatableVariables.table.loading = false;
                })
                .catch((error) => {
                    datatableVariables.table.loading = false;
                });
        };

        watch(props, (newVal, oldVal) => {
            // Reset Selected Rows
            resetSelectedRows();

            initialSetup();
            restSelectedItem();
        });

        watch(selectedWarehouse, (newVal, oldVal) => {
            resetSelectedRows();

            datatableVariables.table.pagination.current = 1;
            setUrlData();
        });
        // for payments //
        const addEditVisible = ref(false);
        const addEditType = ref("add");
        const addEditUrl = ref("order-payments");
        const formData = ref({});
        const editItemAmount = ref(0);

        const pageTitle = computed(() => {
            return addEditType.value == "add"
                ? t(`payments.add`)
                : t(`payments.edit`);
        });

        const successMessage = computed(() => {
            return addEditType.value == "add"
                ? t(`payments.created`)
                : t(`payments.updated`);
        });

        const addItem = (record) => {
            selectedItem.value = record;
            addEditUrl.value = `order-payments`;
            addEditType.value = "add";
            formData.value = {
                ...initPaymentData,
                order_id: selectedItem.value.xid,
            };
            addEditVisible.value = true;
            editItemAmount.value = 0;
            setUrlData();
        };

        const addEditSuccess = (id) => {
            setUrlData();

            // If add action is performed then move page to first
            if (addEditType.value == "add") {
                formData.value = {
                    order_id: selectedItem.value.xid,
                    date: undefined,
                    payment_mode_id: undefined,
                    amount: "",
                    notes: "",
                };
            }
            addEditVisible.value = false;
        };

        const onCloseAddEdit = () => {
            formData.value = {
                ...initPaymentData,
                order_id: selectedItem.value.xid,
            };
            addEditVisible.value = false;
            editItemAmount.value = 0;
        };
        const totals = computed(() => {
            let totalAmount = 0;
            let dueAmount = 0;
            let paidAmount = 0;
            datatableVariables.table.data.forEach((tableRowData) => {
                totalAmount += tableRowData.total;
                dueAmount += tableRowData.due_amount;
                paidAmount += tableRowData.paid_amount;
            });
            return {
                totalAmount,
                dueAmount,
                paidAmount,
            };
        });

        return {
            columns,
            ...datatableVariables,
            filterableColumns,
            pageObject,
            totals,
            formatDate,
            orderStatus,
            orderStatusColors,

            setUrlData,
            formatAmountCurrency,
            invoiceBaseUrl,
            permsArray,

            selectedItem,
            viewItem,
            restSelectedItem,
            paymentSuccess,

            showDeleteConfirm,
            showSelectedDeleteConfirm,

            detailsDrawerVisible,
            onDetailDrawerClose,
            orderItemDetailsColumns,
            selectedLang,
            initialSetup,

            convertToSale,

            // For Online Orders
            confirmOrder,
            cancelOrder,
            viewOrder,
            confirmDelivery,
            changeOrderStatus,
            confirmModalVisible,
            viewModalVisible,
            modalData,
            // End For Online Orders
            getCheckboxProps,

            //for payment//
            initPaymentData,
            addEditSuccess,
            addEditType,
            addEditUrl,
            addEditVisible,
            formData,
            onCloseAddEdit,
            successMessage,
            editItemAmount,
            pageTitle,
            addItem,
            printInvoiceModalVisible,
            payNowVisible,
            posView,
            printInvoiceOrder,
            paymentModalVisible,
            viewPaymentDetails,
            closePaymentDetails,
            userVisible,
            userView,

            printInvoicePDF,
        };
    },
};
</script>
