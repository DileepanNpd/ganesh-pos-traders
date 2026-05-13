import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const { t } = useI18n();
    const { formatAmountCurrency, formatDate } = common();

    const columns = [
        {
            title: t("menu.sales"),
            dataIndex: "sales",
            dbKey: "sales",
            dataFormat: (row) => {
                return formatAmountCurrency(row.sales);
            },
            sorter:true
        },
        {
            title: t("menu.purchases"),
            dataIndex: "purchases",
            dbKey: "purchases",
            dataFormat: (row) => {
                return formatAmountCurrency(row.purchases);
            },
            sorter:true
        },
        {
            title: t("menu.purchase_returns"),
            dataIndex: "purchase_returns",
            dbKey: "purchase_returns",
            dataFormat: (row) => {
                return formatAmountCurrency(row.purchase_returns);
            },
            sorter:true
        },
        {
            title: t("menu.sales_returns"),
            dataIndex: "sales_returns",
            dbKey: "sales_returns",
            dataFormat: (row) => {
                return formatAmountCurrency(row.sales_returns);
            },
            sorter:true
        },
        {
            title: t("menu.expenses"),
            dataIndex: "expenses",
            dbKey: "expenses",
            dataFormat: (row) => {
                return formatAmountCurrency(row.expenses);
            },
            sorter:true
        },
        {
            title: t("common.profit"),
            dataIndex: "profit",
            dbKey: "profit",
            dataFormat: (row) => {
                return formatAmountCurrency(row.profit);
            },
            sorter:true
        },
    ];

    const dateWiseColumns = [
        {
            title: t("common.date"),
            dataIndex: "date",
            dbKey: "date",
            dataFormat: (row) => {
                return formatDate(row.date);
            },
            sorter:true
        },
        {
            title: t("menu.sales"),
            dataIndex: "sales",
            dbKey: "sales",
            dataFormat: (row) => {
                return formatAmountCurrency(row.result.sales);
            },
            sorter:true
        },
        {
            title: t("menu.purchases"),
            dataIndex: "purchases",
            dbKey: "purchases",
            dataFormat: (row) => {
                return formatAmountCurrency(row.result.purchases);
            },
            sorter:true
        },
        {
            title: t("menu.purchase_returns"),
            dataIndex: "purchase_returns",
            dbKey: "purchase_returns",
            dataFormat: (row) => {
                return formatAmountCurrency(row.result.purchase_returns);
            },
            sorter:true
        },
        {
            title: t("menu.sales_returns"),
            dataIndex: "sales_returns",
            dbKey: "sales_returns",
            dataFormat: (row) => {
                return formatAmountCurrency(row.result.sales_returns);
            },
            sorter:true
        },
        {
            title: t("menu.expenses"),
            dataIndex: "expenses",
            dbKey: "expenses",
            dataFormat: (row) => {
                return formatAmountCurrency(row.result.expenses);
            },
            sorter:true
        },
        {
            title: t("common.profit"),
            dataIndex: "profit",
            dbKey: "profit",
            dataFormat: (row) => {
                return formatAmountCurrency(row.result.profit);
            },
            sorter:true
        },
    ]

    return {
        columns,
        dateWiseColumns,
    }
}

export default fields;
