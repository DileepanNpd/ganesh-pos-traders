import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "pre-payments?fields=id,xid,user_id,x_user_id,user{id,xid,name},payment_mode_id,x_payment_mode_id,paymentMode{id,xid,name},amount,notes,payroll_month,payroll_year,date_time";
    const addEditUrl = "pre-payments";
    const { t } = useI18n();
    const hashableColumns = ["payment_mode_id", "user_id"];

    const initData = {
        user_id: undefined,
        payment_mode_id: undefined,
        amount: "",
        date_time: "",
        notes: "",
        payroll_month: "",
        payroll_year: "",
        deduct_from_payroll: 1,
    };

    const columns = ref([
        {
            title: t("pre_payment.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("pre_payment.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("pre_payment.amount"),
            dataIndex: "amount",
        },
        {
            title: t("pre_payment.payment_mode_id"),
            dataIndex: "payment_mode_id",
        },
        {
            title: t("pre_payment.deduct_month"),
            dataIndex: "deduct_month",
        },

        {
            title: t("pre_payment.notes"),
            dataIndex: "notes",
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
