<template>
    <a-typography-link v-if="showLink">
       <UserLogo :user="user" :size="size" />
    </a-typography-link>
	<template v-else>
		<UserLogo :user="user" :size="size" />
	</template>
</template>

<script>
import UserLogo from "./UserLogo.vue";

export default {
	 props: {
        user: {
            default: {},
        },
		size: {
            default: 30,
        },
        showLink: {
            default: true,
        },
    },
	components: {
		UserLogo
	}
};
</script>
