<script>
	import Logobar from "./logobar.svelte";
	import Navbar from "./navbar.svelte";
	import Titlebar from "./titlebar.svelte";
	import Topbar from "./topbar.svelte";

</script>
<main>
    <div class="topbar">
        <Topbar/>
    </div>

    <div class="titlebar">
        <Titlebar/>
    </div>

    <!-- comp nav  -->
    <div class="navbar hidden md:block">
        <Navbar/>
    </div>
    <!-- comp nav  -->

</main>