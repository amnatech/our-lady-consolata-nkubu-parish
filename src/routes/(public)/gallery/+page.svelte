<script>
	import Gallery from "$lib/components/gallery.svelte";
	import Logobar from "$lib/components/logobar.svelte";
	import Pageslider from "$lib/components/pageslider.svelte";

     let title="Gallery";

    let subtitle="gallery albums";

    let tagline="Explore, Experience Our Gallery Albums";

</script>
<main>
    <div class="">
        <Pageslider {title} {tagline}/>
    </div>

    <div class="content">
    <div class="my-4">
        <Gallery title={"Albums"} {subtitle}/>
    </div>
    </div>

</main>