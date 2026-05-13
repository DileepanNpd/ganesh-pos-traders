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
                    @click="$router.push({ name: 'admin.hrm.attendance.details' })"
                    key="attendance_details"
                >
                    <template #icon>
                        <ClockCircleOutlined />
                    </template>
                    {{ $t("menu.attendance_details") }}
                </a-menu-item>
                <a-menu-item
                    @click="$router.push({ name: 'admin.hrm.attendance.summary' })"
                    key="attendance_summary"
                >
                    <template #icon>
                        <TabletOutlined />
                    </template>
                    {{ $t("menu.attendance_summary") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('attendances_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.attendances.index' })"
                    key="attendances"
                >
                    <template #icon>
                        <BarsOutlined />
                    </template>
                    {{ $t("menu.attendances") }}
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
    ClockCircleOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import common from "../../../../common/composable/common";

export default defineComponent({
    components: {
        ScheduleOutlined,
        TabletOutlined,
        BarsOutlined,
        ClockCircleOutlined,
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
