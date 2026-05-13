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
                    @click="$router.push({ name: 'admin.hrm.all-holidays.index' })"
                    key="all_holidays"
                >
                    <template #icon>
                        <BarsOutlined />
                    </template>
                    {{ $t("menu.all_holidays") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('holidays_create') ||
                        permsArray.includes('holidays_edit') ||
                        permsArray.includes('holidays_delete') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.holidays.index' })"
                    key="holidays"
                >
                    <template #icon>
                        <ScheduleOutlined />
                    </template>
                    {{ $t("menu.holidays") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('mark_weekend_holiday') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.weekends.index' })"
                    key="weekends"
                >
                    <template #icon>
                        <TabletOutlined />
                    </template>
                    {{ $t("menu.weekends") }}
                </a-menu-item>
            </a-menu>
        </perfect-scrollbar>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { ScheduleOutlined, TabletOutlined, BarsOutlined } from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import common from "../../../../common/composable/common";

export default defineComponent({
    components: {
        ScheduleOutlined,
        TabletOutlined,
        BarsOutlined,
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
