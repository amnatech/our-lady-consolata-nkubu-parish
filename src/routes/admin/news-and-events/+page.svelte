<script>
	import Segment from "$lib/components/segment.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource, post_resource } from "$lib/methods/functions";
	import { onMount } from "svelte";
    import pkg from 'notiflix';
	import { goto } from "$app/navigation";
	import { current_liu, format_date, get_news_and_events } from "$lib/methods/methods";
    
    const { Notify, Confirm } = pkg;
    
    const URL=`${API_BASE_URL}news-and-events.php`;

    const RESOURCE="News And Evente";

    const liu=current_liu();

    let title="News And Events";

    let subtitle="Manage News And Events";

    let newsAndEvents=[];

    let showNoeModal=false;

    let selectedNoe=null;

    // actions =  create, view, update , delete

    let activityAction="create";

    const delete_news_and_event=async(noe)=>{

        let dt={
            action:"delete",
            noe_id:noe.noe_id,
            title:noe.title,
            deleted_by:liu.name
        }

        try {

            let headers={
                'Content-Type':'application/x-www-form-urlencoded'
            }

            const RESPONSE=await post_resource(RESOURCE,URL,dt,headers);

            const RES=RESPONSE.data;

            console.log(RES);

            if(RES.success){
                Notify.success(RES.message)

                newsAndEvents=await get_news_and_events();
            }else{
                Notify.failure(RES.message)
            }
            
        } catch (err) {
            console.log(err)
        }
    }

    onMount(async()=>{

        newsAndEvents=await get_news_and_events();
    })

</script>
<main>

    <div class="">
        <Title {title} {subtitle}>
            <div class="" slot="action">
               
            </div>
        </Title>
    </div>

    <div class="my-2 md:pl-2">
        <Segment>
            <div class="" slot="title">
                News And Events List
            </div>
            <div class="" slot="actions">
               <a href="news-and-events/create">
                 <button class="ui mini icon purple basic button">
                    <i class="plus icon"></i> Add
                </button>
               </a>
            </div>
            <div class="m-2" slot="content">
               <table class="ui compact basic striped unstackable table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>Venue</th>
                            <th>Date</th>

                            <th>Start</th>
                            <th>End</th>

                            <th>Created</th>


                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        {#each newsAndEvents as a,i}
                            <tr>
                                <td>{i+1}</td>
                                <td>{a.title}</td>
                                <td>{a.venue}</td>
                                <td>{a.noe_date}</td>

                                <td>{a.start_time}</td>
                                <td>{a.end_time}</td>
                                <td>{format_date(a.created_on)}</td>

                                <td>
                                    <div class="">
                                        <!-- svelte-ignore a11y_click_events_have_key_events -->
                                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                                        <i  class="red trash icon cursor-pointer" onclick={()=>{
                                            Confirm.show(
                                                "Delete Activity",
                                                `Delete Activity ${a.title}? This Action Cannot Be Undone`,
                                                "Yes",
                                                "No",
                                                ()=>{
                                                    delete_news_and_event(a);
                                                },
                                                ()=>{
                                                    //do nothing
                                                }
                                            )
                                        }}></i>

                                        <i class="yellow edit icon  cursor-pointer"></i>

                                        <i class="green eye icon   cursor-pointer"></i>

                                    </div>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
               </table>
            </div>
        </Segment>
    </div>
</main>

