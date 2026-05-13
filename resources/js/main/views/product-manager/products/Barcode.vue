<template>
    <div>
        <a-button type="text" size="small" @click="showModal">
            <template #icon>
                <BarcodeOutlined />
            </template>
            {{ $t("product.view_barcode") }}
        </a-button>

        <div id="printThis" style="display: none">
            <BarcodeGenerator
                v-for="n in 24"
                :key="n"
                :value="itemCode + ''"
                :format="barcodeSymbology"
                :height="75"
                :elementTag="'svg'"
            />
        </div>

        <a-modal
            v-model:open="visible"
            :title="$t('product.barcode')"
            :footer="false"
            @ok="handleOk"
        >
            <p style="text-align: center">
                <BarcodeGenerator
                    :value="itemCode + ''"
                    :format="barcodeSymbology"
                    :height="75"
                    :elementTag="'svg'"
                />
            </p>
            <a-button type="primary" @click="printDiv('printThis')" block>
                <template #icon>
                    <PrinterOutlined />
                </template>
                {{ $t("product.print_barcode") }}
            </a-button>
        </a-modal>
    </div>
</template>
<script>
import { defineComponent, ref } from "vue";
import { BarcodeOutlined, PrinterOutlined } from "@ant-design/icons-vue";
import BarcodeGenerator from "../../../../common/components/barcode/BarcodeGenerator.vue";

export default defineComponent({
    props: ["itemCode", "barcodeSymbology"],
    components: {
        BarcodeOutlined,
        PrinterOutlined,
        BarcodeGenerator,
    },
    setup() {
        const visible = ref(false);

        const showModal = () => {
            visible.value = true;
        };

        const handleOk = (e) => {
            visible.value = false;
        };

        const printDiv = (divName) => {
            var divContents = document.getElementById(divName).innerHTML;
            // var newWindow = window.open("", "_blank", "width=100%");

            // newWindow.document.write("<body >");
            // newWindow.document.write(divContents);
            // newWindow.document.write("</body></html>");
            // newWindow.document.close();
            // newWindow.print();

            const printFrame = document.createElement("iframe");
            printFrame.style.display = "none"; // Hide the iframe
            document.body.appendChild(printFrame);
            printFrame.contentDocument.body.innerHTML = `<body>${divContents}</body>`;
            printFrame.contentWindow.print(); // Trigger print dialog
        };

        return {
            visible,
            showModal,
            handleOk,
            printDiv,
        };
    },
});
</script>
