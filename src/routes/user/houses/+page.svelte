<script>
	import Segment from "$lib/components/segment.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource, post_resource } from "$lib/methods/functions";
	import { onMount } from "svelte";
    import pkg from 'notiflix';
	import { goto } from "$app/navigation";
	import { current_liu, get_houses } from "$lib/methods/methods";
    
    const { Notify, Confirm } = pkg;
    
    const URL=`${API_BASE_URL}houses.php`;

    const RESOURCE="Houses";

    const liu=current_liu();

    let title="Prayer Houses";

    let subtitle="View And Manage Prayer Houses";

    let houses=[];

    let showActivityModal=false;

    let selectedActivity=null;

    // actions =  create, view, update , delete

    let activityAction="create";

    const delete_activity=async(activity)=>{

        let dt={
            action:"delete",
            activity_id:activity.activity_id,
            title:activity.title,
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

                houses=await get_houses();
            }else{
                Notify.failure(RES.message)
            }
            
        } catch (err) {
            console.log(err)
        }
    }

    onMount(async()=>{

        houses=await get_houses();
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
                Prayer House List
            </div>
            <div class="" slot="actions">
               <a href="houses/create">
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Location</th>
                        </tr>
                    </thead>

                    <tbody>
                        {#each houses as a,i}
                            <tr>
                                <td>{i+1}</td>
                                <td>{a.name}</td>
                                <td>{a.email}</td>
                                <td>{a.phone}</td>
                                <td>{a.location}</td>

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
                                                    delete_activity(a);
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

