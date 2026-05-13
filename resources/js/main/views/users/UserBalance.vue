<template>
    <a-tooltip v-if="amount < 0" :title="$t('payments.you_will_pay')">
        <ArrowUpOutlined :style="{ color: 'red' }" />
        {{ formatAmountCurrency(Math.abs(amount)) }}
    </a-tooltip>
    <a-tooltip v-else :title="$t('payments.you_will_receive')">
        <span v-if="amount > 0">
            <ArrowDownOutlined :style="{ color: 'green' }" />
        </span>
        {{ formatAmountCurrency(amount) }}
    </a-tooltip>
</template>

<script>
import { defineComponent, ref } from "vue";
import { ArrowDownOutlined, ArrowUpOutlined } from "@ant-design/icons-vue";
import common from "../../../common/composable/common";

export default defineComponent({
    props: ["amount"],
    components: {
        ArrowDownOutlined,
        ArrowUpOutlined,
    },
    setup() {
        const { formatAmountCurrency } = common();

        return {
            formatAmountCurrency,
        };
    },
});
</script>
