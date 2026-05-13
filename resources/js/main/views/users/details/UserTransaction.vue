<template>
    <a-modal
        :open="visible"
        :centered="true"
        :title="$t('payments.transactions')"
        @cancel="onClose"
        width="40%"
        :footer="false"
    >
        <Transactions
            :user_id="user.xid"
            :result_type="
                user.user_type == 'staff_members'
                    ? 'staff_member'
                    : 'customer_supplier'
            "
        />
    </a-modal>
</template>

<script>
import { defineComponent, ref } from "vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import Transactions from "./Transactions.vue";

export default defineComponent({
    props: ["visible", "user",],
    components: {
        Transactions,
    },
    setup(props, { emit }) {
        const { loading, rules } = apiAdmin();
       
        const onClose = () => {
            emit("closed");
        };

        return {
            loading,
            rules,
            onClose,
        };
    },
});
</script>
