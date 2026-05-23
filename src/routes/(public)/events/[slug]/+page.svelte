<script>
	import { goto } from "$app/navigation";
	import { page } from "$app/state";
	import Gallery from "$lib/components/gallery.svelte";
	import Logobar from "$lib/components/logobar.svelte";
	import Pageslider from "$lib/components/pageslider.svelte";
	import Segment from "$lib/components/segment.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource } from "$lib/methods/functions";
	import { get_news_and_events } from "$lib/methods/methods";
	import { onMount } from "svelte";

    const URL=`${API_BASE_URL}news-and-events.php?noe=${page.params.slug}`;

    const RESOURCE="News And Events";

     let title;

    let subtitle;

    let tagline;

    let noe;

    let newsAndEvents=[];

    const get_noe=async(page)=>{
        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data.data;
            
        } catch (err) {
            console.log(err)
        }
    }

    onMount(async()=>{
        noe=await get_noe(page);
        newsAndEvents=await get_news_and_events();

        title=noe.title;
        tagline=noe.subtitle;
        subtitle=noe.subtitle

        console.log(noe);
    })

</script>
<main>
    <div class="">
        <Pageslider {title} {tagline}/>
    </div>

        <div class="content py-5">
        <div class="my-2">
                    {#if noe}
            <Segment>
                <div class="flex flex-col md:flex-row" slot="content">
                    <div class="flex-1">
                        <div class="p-2 rounded shadow-lg">
                            <img src="{API_BASE_URL}{noe.featured_image}" alt="{noe.title}">
                        </div>
                    </div>
                    <div class="flex-2 p-2">
                        <div class="text-2xl font-extrabold">
                            {noe.title}
                        </div>

                        <div class="italic font-light">
                            {noe.subtitle}
                        </div>
                        <div class="flex gap-4 my-2">
                            <div class="">
                               <i class="marker icon"></i>: &nbsp; {noe.venue}
                            </div>
                            <div class="">
                               <i class="calendar icon"></i>: &nbsp; {noe.noe_date}
                            </div>

                            <div class="">
                                <i class="clock icon"></i>: &nbsp; {noe.start_time} - {noe.end_time}
                            </div>

                        </div>

                        <div class="py-4 description">
                            {@html noe.description}
                        </div>
                    </div>
                </div>
            </Segment>
        {/if}
        </div>

        <div class="my-2">
            
            <Segment>
                .
                <div class="" slot="content">
                    <div class="py-5">
                        <div class="title text-2xl text-center font-bold">
                            <span class="text-wekebio-red">More News</span> <span class="text-wekebio-pastel">& Events</span>
                        </div>
                        <div class="hline border-t-6 w-18 m-auto border-wekebio-purple">
                            
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row flex-wrap justify-between items-center gap-4 my-5">
                        {#each newsAndEvents as noe}
                                <div class="flex flex-col justify-center items-center flex-1 gap-3 bg-slate-50 hover:bg-slate-100 cursor-pointer rounded  shadow">
                                    <div class="image  p-3">
                                        <img class="h-36" src="{API_BASE_URL}{noe.featured_image}" alt="{noe.title}">
                                    </div>
                                    <div class="title">
                                        {noe.title}
                                    </div>
                                    <!-- svelte-ignore a11y_no_static_element_interactions -->
                                    <div class="p-2">
                                        <!-- svelte-ignore a11y_click_events_have_key_events -->
                                        <!-- svelte-ignore a11y_missing_attribute -->
                                        <a onclick={()=>{
                                            window.location.href=noe.slug;
                                        }} class="font-semibold text-wekebio-purple cursor-pointer">
                                            Visit  <i class="right arrow icon"></i>  
                                        </a>
                                    </div>
                                </div>
                        {/each}
                    </div>
   
                </div>
            </Segment>
        </div>

        </div>


</main>

<style>
    .description{
        line-height: 2.5em;
    }
</style>