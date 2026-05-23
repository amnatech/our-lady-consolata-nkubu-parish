<script>
	import Pageslider from "$lib/components/pageslider.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { get_news_and_events } from "$lib/methods/methods";
	import dayjs from "dayjs";
	import { onMount } from "svelte";

    let title="News And Events";

    let tagline="Explore Upcoming Events";

    let newsAndEvents=[];

    const format_noes=(noes)=>{
        const formated=noes.map((noe)=>{
            return{
                month:dayjs(noe.noew_date).format('MMM'),
                day:dayjs(noe.noe_date).format('DD'),
                title:noe.title,
                subtitle:noe.subtitle,
                description:noe.description,
                location:noe.venue,
                time:`${noe.start_time} - ${noe.end_time}`,
                link:noe.slug
            }
        })

        return formated;
    }

    onMount(async()=>{

        newsAndEvents=format_noes(await get_news_and_events());

    })
</script>

<main>
    <!-- slider  -->
    <div class="">
        <Pageslider {title} {tagline}/>
    </div>
    <!-- slider  -->
     <br>
    <div class="content my-4 p-5">
        <div class="py-2">
            <div class="title text-2xl text-center font-bold m-2">
                 <span class="text-wekebio-red">News </span> <span class="text-wekebio-pastel"> And Events</span>
                </div>
                <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
                    <br>
                </div>

        </div>

        <div class="max-w-200 m-auto">
                <div class="events-timeline">
        {#each newsAndEvents as noe}
            <div class="event-item">
                <div class="event-date">
                    <span class="month">{noe.month}</span>
                    <span class="day">{noe.day}</span>
                </div>
                <div class="event-content">
                    <h3>{noe.title}</h3>
                    <p class="event-time">⏰ {noe.time} | 📍 {noe.location}</p>
                    <p class="event-description">{noe.description}</p>
                    <a href={`events/${noe.link}` || '#'} class="event-link">Learn More →</a>
                </div>
            </div>
        {/each}
    </div>
        </div>
      
    </div>

</main>



<style>

    .events-timeline {
        position: relative;
    }
    
    .events-timeline::before {
        content: '';
        position: absolute;
        left: 90px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: rgba(255, 255, 255, 0.3);
    }
    
    .event-item {
        display: flex;
        margin-bottom: 3rem;
        position: relative;
    }
    
    .event-date {
        width: 80px;
        text-align: center;
        margin-right: 2rem;
        display: flex;
        flex-direction: column;
    }
    
    .month {
        background: linear-gradient(45deg, #2CB34Aff, #5c4b9a);
        color: white;
        padding: 0.3rem 0.5rem;
        border-radius: 5px 5px 0 0;
        font-size: 0.9rem;
        font-weight: bold;
    }
    
    .day {
        background: white;
        color: #333;
        padding: 0.5rem;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 0 0 5px 5px;
    }
    
    .event-content {
        flex: 1;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        padding: 1.5rem;
        border-radius: 10px;
    }
    
    .event-content h3 {
        color: rgb(63, 63, 63);
        margin: 0 0 0.5rem 0;
    }
    
    .event-time {
        color: #10271d;
        font-size: 0.9rem;
        margin: 0 0 1rem 0;
    }
    
    .event-description {
        color: #305a3d;
        line-height: 1.6;
        margin: 0 0 1rem 0;
    }
    
    .event-link {
        color: var(--color-red-400);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    
    .event-link:hover {
        color: var(--color-red-800);

    }
</style>