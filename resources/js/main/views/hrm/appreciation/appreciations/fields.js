import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "appreciations?fields=id,xid,user_id,x_user_id,user{id,xid,name},award_id,x_award_id,award{id,xid,name},date,description,price_amount,price_given";
    const addEditUrl = "appreciations";
    const { t } = useI18n();
    const hashableColumns = ["user_id", "award_id"];

    const initData = {
        user_id: undefined,
        award_id: undefined,
        date: undefined,
        price_amount: "",
        price_given: "",
        description: "",
    };

    const columns = ref([
        {
            title: t("appreciation.user"),
            dataIndex: "user_id",
        },
        {
            title: t("appreciation.date"),
            dataIndex: "date",
        },
        {
            title: t("appreciation.award"),
            dataIndex: "award_id",
        },
        {
            title: t("appreciation.price_amount"),
            dataIndex: "price_amount",
        },
        {
            title: t("appreciation.price_given"),
            dataIndex: "price_given",
        },
        {
            title: t("appreciation.description"),
            dataIndex: "description",
        },
    ]);

    const filterableColumns = [
        {
            key: "description",
            value: t("common.description"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
