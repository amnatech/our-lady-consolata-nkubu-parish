<script>
	import Logobar from "./logobar.svelte";
	import Navbar from "./navbar.svelte";
	import TopContacts from "./topcontacts.svelte";
	import Topaddress from "./topaddress.svelte";
	import Topsocials from "./topsocials.svelte";
    import {page} from "$app/stores"
	import { onMount } from "svelte";
	import { org_details } from "$lib/methods/methods";

    let showMobileNav=false;

    let organisation;

    // close nave when url changes 
    $:(page)=>{
            console.log(page);
        closeNav();
    }

    const openNav=()=>{
        showMobileNav=true;
    }



    const closeNav=()=>{
        showMobileNav=false;
    }

    onMount(async()=>{
        organisation=await org_details();
    })

</script>
<main class="bg-white shadow">
    <div class="content flex justify-between align-middle">
        <div class="logo">
            <Logobar/>
        </div>
        
        <div class="top_socials hidden md:block">
            <Topsocials/>
        </div>

        <div class="top_contacts hidden md:block">
            {#if organisation}
                <TopContacts {organisation}/>
            {/if}
        </div>

        <div class="top_address hidden md:block">
            {#if organisation}
                 <Topaddress {organisation}/>
            {/if}
           
        </div>

        <div class="mobile_nav_trigger block md:hidden">
            <div class="m-2 p-2 text-2xl font-bold">
                {#if showMobileNav}
                    <!-- svelte-ignore a11y_click_events_have_key_events -->
                    <!-- svelte-ignore a11y_no_static_element_interactions -->
                    <i onclick={closeNav} class="ri-close-large-line"></i>
                    {:else}
                    <!-- svelte-ignore a11y_click_events_have_key_events -->
                    <!-- svelte-ignore a11y_no_static_element_interactions -->
                    <i onclick={openNav}  class="ri-menu-2-line"></i>

                {/if}
            </div>
        </div>
    </div>
</main>

<!-- mobile nav  -->
{#if showMobileNav}
    <div class="mobile_nav">
       <Navbar on:navigate={closeNav}/>
    </div>
{/if}
<!-- mobile nav  -->

