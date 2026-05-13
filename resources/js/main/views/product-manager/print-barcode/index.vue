<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.print_barcodes`)" class="p-0">
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.print_barcodes`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-table-content>
        <a-card class="page-content-container mt-20 mb-20">
            <a-form layout="vertical">
                <a-row :gutter="16" class="mb-20">
                    <a-col :xs="24" :sm="24" :md="12" :lg="24" :xl="24">
                        <ProductSearchInput @valueChanged="productSelected" />
                    </a-col>
                </a-row>

                <a-row :gutter="16">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-table
                            :row-key="(record) => record.xid"
                            :dataSource="selectedProducts"
                            :columns="orderItemColumns"
                            :pagination="false"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'name'">
                                    {{ record.name }} <br />
                                </template>
                                <template
                                    v-if="column.dataIndex === 'unit_quantity'"
                                >
                                    <a-input-number
                                        id="inputNumber"
                                        v-model:value="record.quantity"
                                        :min="1"
                                        @change="quantityChanged(record)"
                                    />
                                </template>
                                <template v-if="column.dataIndex === 'action'">
                                    <a-button
                                        type="primary"
                                        @click="showDeleteConfirm(record)"
                                        style="margin-left: 4px"
                                    >
                                        <template #icon
                                            ><DeleteOutlined
                                        /></template>
                                    </a-button>
                                </template>
                            </template>
                        </a-table>
                    </a-col>
                </a-row>

                <a-row :gutter="16" class="mt-20">
                    <a-col :xs="24" :sm="24" :md="4" :lg="4"
                        ><a-checkbox v-model:checked="selectName">
                            {{ $t("print_barcode.select_name") }}</a-checkbox
                        >
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="4" :lg="4">
                        <a-checkbox v-model:checked="selectPrice">
                            {{ $t("print_barcode.select_price") }}</a-checkbox
                        ></a-col
                    >
                </a-row>

                <a-row :gutter="16" class="mt-20">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8">
                        <a-form-item
                            :label="$t('print_barcode.paper_size')"
                            name="paper_size"
                        >
                            <a-select
                                v-model:value="perSheetBarcode"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('print_barcode.paper_size'),
                                    ])
                                "
                                @change="barcodePerSheetSelected"
                            >
                                <a-select-option
                                    v-for="allPaperSize in paperSizeArray"
                                    :key="`size-${allPaperSize.value}`"
                                    :value="allPaperSize.value"
                                >
                                    {{ allPaperSize.label }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
            </a-form>
            <div>
                <a-space>
                    <a-button
                        style="color: white; background-color: #e81515"
                        @click="reset"
                    >
                        {{ $t("common.reset") }}
                        <template #icon>
                            <LinkOutlined />
                        </template>
                    </a-button>
                    <a-button type="primary" @click="printBarcodes" block>
                        <template #icon>
                            <PrinterOutlined />
                        </template>
                        {{ $t("product.print_barcode") }}
                    </a-button>
                </a-space>
            </div>

            <div class="mt-30" ref="contentToPrint">
                <div
                    class="give-border"
                    :style="a4pageStyleObject"
                    v-for="allBarcode in allBarcodes"
                    :key="allBarcode.xid"
                >
                    <div
                        :style="barcodeStyleObject"
                        v-for="allBarcodeObject in allBarcode"
                        :key="allBarcodeObject.xid"
                    >
                        <div style="padding-left: 10px; font-weight: bold">
                            <div
                                style="text-align: center"
                                v-if="selectName == true"
                            >
                                {{ allBarcodeObject.name }}
                            </div>
                            <div
                                v-if="
                                    allBarcodeObject &&
                                    allBarcodeObject.price != ''
                                "
                                style="text-align: center"
                            >
                                <div v-if="selectPrice == true">
                                    {{
                                        formatAmountCurrency(
                                            allBarcodeObject.price
                                        )
                                    }}
                                </div>
                            </div>
                            <BarcodeGenerator
                                :value="allBarcodeObject.item_code + ''"
                                :format="allBarcodeObject.barcode_symbology"
                                :height="18"
                                :width="1"
                                :fontSize="16"
                                :elementTag="'svg'"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </a-card>
    </admin-page-table-content>
</template>

<script>
import { onMounted, ref, toRefs, createVNode, nextTick } from "vue";
import {
    EyeOutlined,
    PlusOutlined,
    SearchOutlined,
    SaveOutlined,
    LoadingOutlined,
    PrinterOutlined,
    LinkOutlined,
    BarcodeOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ProductSearchInput from "./ProductSearchInput.vue";
import { Modal } from "ant-design-vue";
import BarcodeGenerator from "../../../../common/components/barcode/BarcodeGenerator.vue";
import { forEach, find } from "lodash-es";

export default {
    components: {
        EyeOutlined,
        PlusOutlined,
        DeleteOutlined,
        ExclamationCircleOutlined,
        SearchOutlined,
        SaveOutlined,
        LoadingOutlined,
        BarcodeOutlined,
        PrinterOutlined,
        LinkOutlined,

        BarcodeGenerator,
        AdminPageHeader,
        ProductSearchInput,
    },
    setup() {
        const { appSetting, permsArray, formatAmountCurrency } = common();
        const { t } = useI18n();
        const selectedProducts = ref([]);

        const perSheetBarcode = ref(40);
        const a4SheetClassName = ref("");
        const barcodeClassName = ref("");
        const allBarcodes = ref([]);
        const barcodeStyleObject = ref({});
        const a4pageStyleObject = ref({});
        const contentToPrint = ref(null);
        const selectName = ref(false);
        const selectPrice = ref(false);

        const paperSizeArray = ref([
            { label: "40 per sheet (a4) (1.799 * 1.003)", value: 40 },
            { label: "30 per sheet (2.625 * 1)", value: 30 },
            { label: "24 per sheet (a4) (2.48 * 1.334)", value: 24 },
            { label: "20 per sheet (4 * 1)", value: 20 },
            { label: "18 per sheet (a4) (2.5 * 1.835)", value: 18 },
            { label: "14 per sheet (4 * 1.33)", value: 14 },
            { label: "12 per sheet (a4) (2.5 * 2.834)", value: 12 },
            { label: "10 per sheet (4 * 2)", value: 10 },
        ]);

        const orderItemColumns = [
            {
                title: "#",
                dataIndex: "sn",
            },
            {
                title: t("print_barcode.name"),
                dataIndex: "name",
            },
            {
                title: t("print_barcode.quantity"),
                dataIndex: "unit_quantity",
            },
            {
                title: t("common.action"),
                dataIndex: "action",
            },
        ];

        const quantityChanged = (record) => {
            forEach(selectedProducts.value, (selectedProduct) => {
                if (selectedProduct.xid == record.xid) {
                    selectedProduct.quantity = record.quantity;
                }
            });

            barcodePerSheetSelected();
        };

        const reset = () => {
            selectedProducts.value = [];
            allBarcodes.value = [];
        };

        const showDeleteConfirm = (product) => {
            // Delete selected product and rearrange SN
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("print_barcode.delete_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    const newResults = [];
                    let counter = 1;
                    selectedProducts.value.map((selectedProduct) => {
                        if (selectedProduct.xid != product.xid) {
                            newResults.push({
                                ...selectedProduct,
                                sn: counter,
                            });

                            counter++;
                        }
                    });
                    selectedProducts.value = newResults;

                    barcodePerSheetSelected();
                },
            });
        };

        const productSelected = (product) => {
            const productFound = find(selectedProducts.value, {
                xid: product.product.xid,
            });

            if (productFound === undefined) {
                selectedProducts.value = [
                    ...selectedProducts.value,
                    {
                        sn: selectedProducts.value.length + 1,
                        xid: product.product.xid,
                        name: product.product.name,
                        item_code: product.product.item_code,
                        price:
                            product.product &&
                            product.product.details &&
                            product.product.details.sales_price
                                ? product.product.details.sales_price
                                : "",
                        quantity: 10,
                        barcode_symbology: product.product.barcode_symbology,
                    },
                ];
            }

            barcodePerSheetSelected();
        };

        const chunkArray = (array, size) => {
            const chunks = [];
            let index = 0;

            while (index < array.length) {
                chunks.push(array.slice(index, index + size));
                index += size;
            }

            return chunks;
        };

        const barcodePerSheetSelected = () => {
            const a4RegularStyle = {
                height: "11.6in",
                padding: ".1in 0 0 .1in",
                width: "8.25in",
                display: "block",
                margin: "10px auto",
                pageBreakAfter: "always",
            };

            const a4nonRegularStyle = {
                height: "10.3in",
                paddingTop: ".1in",
                width: "8.45in",

                display: "block",
                margin: "10px auto",
                pageBreakAfter: "always",
            };

            const commonBarcodeBoxStyle = {
                border: "1px dotted #ccc",
                display: "block",
                float: "left",
                fontSize: "12px",
                lineHeight: "14px",
                overflow: "hidden",
                textAlign: "center",
                textTransform: "uppercase",
            };

            if (perSheetBarcode.value == 40) {
                barcodeStyleObject.value = {
                    height: "1.003in",
                    margin: "0 .07in",
                    paddingTop: ".05in",
                    width: "1.799in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4RegularStyle;
            } else if (perSheetBarcode.value == 30) {
                barcodeStyleObject.value = {
                    height: "1in",
                    margin: "0 .07in",
                    paddingTop: ".05in",
                    width: "2.625in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4nonRegularStyle;
            } else if (perSheetBarcode.value == 24) {
                barcodeStyleObject.value = {
                    height: "1.335in",
                    marginLeft: ".079in",
                    paddingTop: ".05in",
                    width: "2.48in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4RegularStyle;
            } else if (perSheetBarcode.value == 20) {
                barcodeStyleObject.value = {
                    height: "1in",
                    margin: "0 .1in",
                    paddingTop: ".05in",
                    width: "4in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4nonRegularStyle;
            } else if (perSheetBarcode.value == 18) {
                barcodeStyleObject.value = {
                    fontSize: "13px",
                    height: "1.835in",
                    lineHeight: "20px",
                    marginLeft: ".079in",
                    paddingTop: ".05in",
                    width: "2.5in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4RegularStyle;
            } else if (perSheetBarcode.value == 14) {
                barcodeStyleObject.value = {
                    height: "1.33in",
                    margin: "0 .1in",
                    paddingTop: ".1in",
                    width: "4in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4nonRegularStyle;
            } else if (perSheetBarcode.value == 12) {
                barcodeStyleObject.value = {
                    fontSize: "14px",
                    height: "2.834in",
                    lineHeight: "20px",
                    marginLeft: ".079in",
                    paddingTop: ".05in",
                    width: "2.5in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4RegularStyle;
            } else if (perSheetBarcode.value == 10) {
                barcodeStyleObject.value = {
                    fontSize: "14px",
                    height: "2in",
                    lineHeight: "20px",
                    margin: "0 .1in",
                    paddingTop: ".1in",
                    width: "4in",
                    ...commonBarcodeBoxStyle,
                };

                a4pageStyleObject.value = a4nonRegularStyle;
            }

            var allBarcodesData = [];
            selectedProducts.value.map((selectedProduct) => {
                for (let i = 0; i < selectedProduct.quantity; i++) {
                    allBarcodesData.push({
                        xid: selectedProduct.xid,
                        name: selectedProduct.name,
                        item_code: selectedProduct.item_code,
                        barcode_symbology: selectedProduct.barcode_symbology,
                        price: selectedProduct.price,
                    });
                }
            });

            allBarcodes.value = chunkArray(
                allBarcodesData,
                perSheetBarcode.value
            );
        };

        const printBarcodes = () => {
            const printFrame = document.createElement("iframe");
            printFrame.style.display = "none"; // Hide the iframe
            document.body.appendChild(printFrame);
            printFrame.contentDocument.body.innerHTML = `<body>${contentToPrint.value.innerHTML}</body>`;
            printFrame.contentWindow.print(); // Trigger print dialog
        };

        return {
            appSetting,
            formatAmountCurrency,
            orderItemColumns,
            permsArray,
            selectedProducts,
            reset,
            showDeleteConfirm,
            quantityChanged,

            productSelected,

            barcodePerSheetSelected,
            perSheetBarcode,
            a4SheetClassName,
            barcodeClassName,
            allBarcodes,
            barcodeStyleObject,
            a4pageStyleObject,
            paperSizeArray,
            contentToPrint,
            printBarcodes,
            selectPrice,
            selectName,
        };
    },
};
</script>

<style lang="less" scoped>
td {
    border: 1px dotted lightgray;
}
@media print {
    table {
        page-break-after: always;
    }
}

.give-border {
    border: 1px solid #ccc;
}
</style>
