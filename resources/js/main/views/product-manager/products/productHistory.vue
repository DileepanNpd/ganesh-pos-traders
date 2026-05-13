<template>
    <a-modal
        :open="visible"
        :centered="true"
        :title="$t('product.product_history')"
        @cancel="onClose"
        width="40%"
        :footer="false"
    >
      <ProductOrderItems :product="product" />
    </a-modal>
</template>

<script>
import { defineComponent,ref } from "vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import ProductOrderItems from "./details/ProductOrderItems.vue";

export default defineComponent({
    props: ["visible", "product"],
	emits: ["closed"],
    components: {
        ProductOrderItems,
    },
    setup(props, { emit }) {
        const { loading, rules } = apiAdmin();

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            loading,
            rules,
            onClose,
        };
    },
});
</script>
