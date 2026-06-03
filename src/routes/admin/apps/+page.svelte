<script>
	import AdminAppDrawer from "$lib/components/admin-app-drawer.svelte";

	// import UserProfile from "$lib/components/user-profile.svelte";
	import Title from "$lib/components/title.svelte";

</script>
<main class="pl-2">
    <div class="">
    <Title title="Apps" subtitle="View System Applications "/>
    </div>

    <div class="my-2">
        <AdminAppDrawer/>
    </div>
</main>