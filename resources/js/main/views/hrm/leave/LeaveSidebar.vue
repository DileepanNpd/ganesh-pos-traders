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
                    @click="$router.push({ name: 'admin.hrm.leaves.index' })"
                    key="leaves"
                >
                    <template #icon>
                        <ScheduleOutlined />
                    </template>
                    {{ $t("menu.leaves") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('leave_types_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.leave_types.index' })"
                    key="leave_types"
                >
                    <template #icon>
                        <FundViewOutlined />
                    </template>
                    {{ $t("menu.leave_types") }}
                </a-menu-item>
                <a-menu-item
                    @click="$router.push({ name: 'admin.hrm.remaining-leaves.index' })"
                    key="remaining_leaves"
                >
                    <template #icon>
                        <GoldOutlined />
                    </template>
                    {{ $t("menu.remaining_leaves") }}
                </a-menu-item>
                <a-menu-item
                    @click="$router.push({ name: 'admin.hrm.unpaid-leaves.index' })"
                    key="unpaid_leaves"
                >
                    <template #icon>
                        <UnderlineOutlined />
                    </template>
                    {{ $t("menu.unpaid_leaves") }}
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
    FundViewOutlined,
    GoldOutlined,
    UnderlineOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import common from "../../../../common/composable/common";

export default defineComponent({
    components: {
        ScheduleOutlined,
        TabletOutlined,
        FundViewOutlined,
        GoldOutlined,
        UnderlineOutlined,
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
