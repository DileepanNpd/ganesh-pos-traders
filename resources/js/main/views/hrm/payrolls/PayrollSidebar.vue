<template>
    <div class="setting-sidebar">
        <perfect-scrollbar
            :options="{
                wheelSpeed: 1,
                swipeEasing: true,
                suppressScrollX: true,
            }"
        >
            <a-menu v-model:selectedKeys="selectedKeys">
                <a-menu-item
                    v-if="
                        permsArray.includes('basic_salaries_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.basic_salaries.index' })"
                    key="basic_salaries"
                >
                    <template #icon>
                        <DollarCircleOutlined />
                    </template>
                    {{ $t("menu.basic_salaries") }}
                </a-menu-item>
                <a-menu-item
                    @click="$router.push({ name: 'admin.hrm.pre_payments.index' })"
                    key="pre_payments"
                >
                    <template #icon>
                        <BarsOutlined />
                    </template>
                    {{ $t("menu.pre_payments") }}
                </a-menu-item>
                <a-menu-item
                    @click="$router.push({ name: 'admin.hrm.payrolls.index' })"
                    key="payrolls"
                >
                    <template #icon>
                        <ScheduleOutlined />
                    </template>
                    {{ $t("menu.payrolls") }}
                </a-menu-item>
                <a-menu-item
                    @click="$router.push({ name: 'admin.hrm.increments_payments.index' })"
                    key="increments_promotions"
                >
                    <template #icon>
                        <TabletOutlined />
                    </template>
                    {{ $t("menu.increments_promotions") }}
                </a-menu-item>
            </a-menu>
        </perfect-scrollbar>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    ScheduleOutlined,
    TabletOutlined,
    BarsOutlined,
    DollarCircleOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import common from "../../../../common/composable/common";

export default defineComponent({
    components: {
        ScheduleOutlined,
        TabletOutlined,
        BarsOutlined,
        DollarCircleOutlined,
    },
    setup() {
        const { appSetting, user, permsArray, appModules, appType } = common();
        const route = useRoute();
        const store = useStore();
        const selectedKeys = ref([]);

        onMounted(() => {
            const menuKey =
                typeof route.meta.menuKey == "function"
                    ? route.meta.menuKey(route)
                    : route.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        return {
            permsArray,

            selectedKeys,
            appType,
        };
    },
});
</script>

<style lang="less">
.setting-sidebar .ps {
    height: calc(100vh - 145px);
}
</style>
