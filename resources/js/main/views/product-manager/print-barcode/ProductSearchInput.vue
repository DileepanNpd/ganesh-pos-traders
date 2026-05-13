<template>
    <a-select
        v-model:value="searchTerm"
        show-search
        :filter-option="false"
        :placeholder="$t('product.search_scan_product')"
        style="width: 100%"
        :not-found-content="fetching ? undefined : null"
        @search="fetchProducts"
        option-label-prop="label"
        @change="valueChanged"
        :allowClear="true"
        @select="searchValueSelected"
    >
        <template #suffixIcon><SearchOutlined /></template>
        <template v-if="fetching" #notFoundContent>
            <a-spin size="small" />
        </template>
        <a-select-option
            v-for="newProduct in products"
            :key="newProduct.xid"
            :value="newProduct.xid"
            :label="newProduct.name"
            :product="newProduct"
        >
            {{ newProduct.name }}
        </a-select-option>
    </a-select>
</template>

<script>
import { defineComponent, toRefs, reactive, watch, onMounted, ref } from "vue";
import { SearchOutlined } from "@ant-design/icons-vue";
import { debounce, includes } from "lodash-es";

export default defineComponent({
    props: ["productData"],
    emits: ["valueSuccess", "valueChanged", "showProduct"],
    components: {
        SearchOutlined,
    },
    setup(props, { emit }) {
        const state = reactive({
            searchTerm: [],
            fetching: false,
            products: [],
        });

        const listProducts = ref({});

        onMounted(() => {
            resetSearchInput(props);
        });

        const resetSearchInput = (propVal) => {
            if (propVal.productData && propVal.productData.product) {
                state.products = [
                    {
                        xid: propVal.productData.x_product_id,
                        name: propVal.productData.product.name,
                    },
                ];
                state.searchTerm = propVal.productData.x_product_id;
            } else {
                state.searchTerm = [];
                state.products = [];
            }

            emit("valueSuccess");
        };

        const valueChanged = (value, option) => {
            emit("valueChanged", option);
            emit("valueSuccess");
        };

        const fetchProducts = debounce((value) => {
            state.products = [];

            if (value != "") {
                state.fetching = true;
                const filterString = `name lk "%${value}%" or item_code lk "%${value}%"`;
                let url = `products?fields=xid,name,item_code,barcode_symbology,details{sales_price}&filters=${encodeURIComponent(
                    filterString
                )}&limit=10`;

                axiosAdmin.get(url).then((response) => {
                    state.products = response.data;
                    state.fetching = false;
                });
            }
        }, 300);

        const searchValueSelected = (value, option) => {
            listProducts.value = option.product;
            emit("showProduct", listProducts.value);
            state.searchTerm = [];
            state.products = [];
        };

        watch(props, (newVal, oldVal) => {
            resetSearchInput(newVal);
        });

        return {
            ...toRefs(state),
            fetchProducts,
            valueChanged,
            searchValueSelected,
        };
    },
});
</script>
