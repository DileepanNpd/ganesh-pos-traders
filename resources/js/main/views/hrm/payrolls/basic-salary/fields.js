import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "basic-salaries?fields=id,xid,user_id,x_user_id,user{id,xid,name},basic_salary";
    const addEditUrl = "basic-salaries";
    const { t } = useI18n();
    const hashableColumns = ["user_id"];

    const initData = {
        user_id: undefined,
        basic_salary: "",
    };

    const columns = [
        {
            title: t("pre_payment.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("basic_salary.basic_salary"),
            dataIndex: "basic_salary",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
