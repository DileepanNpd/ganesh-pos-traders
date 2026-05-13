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
                    @click="$router.push({ name: 'admin.hrm.awards.index' })"
                    key="awards"
                >
                    <template #icon>
                        <FundViewOutlined />
                    </template>
                    {{ $t("menu.awards") }}
                </a-menu-item>
                <a-menu-item
                    @click="$router.push({ name: 'admin.hrm.appreciations.index' })"
                    key="appreciations"
                >
                    <template #icon>
                        <ScheduleOutlined />
                    </template>
                    {{ $t("menu.appreciations") }}
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
