import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "increments-promotions?fields=id,xid,user_id,x_user_id,user{id,xid,name},promoted_designation_id,x_promoted_designation_id,designation{id,xid,name},current_designation_id,x_current_designation_id,designation{id,xid,name},net_salary,type,description,date";
    const addEditUrl = "increments-promotions";
    const { t } = useI18n();
    const hashableColumns = [
        "current_designation_id",
        "promoted_designation_id",
        "user_id",
    ];

    const initData = {
        user_id: undefined,
        current_designation_id: undefined,
        promoted_designation_id: undefined,
        date: undefined,
        type: "increment",
        description: "",
        net_salary: "",
    };

    const columns = ref([
        {
            title: t("increment_promotion.date"),
            dataIndex: "date",
        },
        {
            title: t("increment_promotion.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("increment_promotion.type"),
            dataIndex: "type",
        },
        {
            title: t("increment_promotion.details"),
            dataIndex: "details",
        },
        {
            title: t("increment_promotion.net_salary"),
            dataIndex: "net_salary",
        },
        {
            title: t("increment_promotion.description"),
            dataIndex: "description",
        },
    ]);

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
