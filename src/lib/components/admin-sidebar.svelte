<script>
	import { createEventDispatcher } from "svelte";
	import AdminAppDrawer from "./admin-app-drawer.svelte";
	import Segment from "./segment.svelte";
	import { page } from '$app/stores';

    const dispatch=createEventDispatcher();

</script>
<main class="fixed top-0 left-0 w-[100vw] h-[100vh] z-1000 bg-[rgba(0,0,0,0.5)]">
<div class="flex justify-end h-full">
    <div class="w-full md:w-[25em] bg-slate-50">
        <Segment>
            <div class="" slot="title">
                <i class="expand icon"></i> Quick Navigation
            </div>

            <div class="text-2xl" slot="actions">
                <!-- svelte-ignore a11y_click_events_have_key_events -->
                <!-- svelte-ignore a11y_no_static_element_interactions -->
                <i onclick={()=>{
                    dispatch("close")
                }} class="ui red  x icon cursor-pointer"></i>
            </div>

            <div class="" slot="content">
                    <div class="my-2">
                        <AdminAppDrawer/>
                    </div>
            </div>
        </Segment>
    </div>
</div>
</main>