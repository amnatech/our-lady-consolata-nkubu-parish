<script>
	import Segment from "$lib/components/segment.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource, post_resource } from "$lib/methods/functions";
	import { onMount } from "svelte";
    import pkg from 'notiflix';
	import { goto } from "$app/navigation";
	import { current_liu, format_date, get_messages, get_news_and_events } from "$lib/methods/methods";
    
    const { Notify, Confirm } = pkg;
    
    const URL=`${API_BASE_URL}messages.php`;

    const RESOURCE="Messages";

    const liu=current_liu();

    let title="Messages";

    let subtitle="View & Reply To Messages";

    let messages=[];

    let showMessageModal=false;

    let selectedMessage=null;

    // actions =  create, view, update , delete

    let messageAction="create";

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

                messages=await get_news_and_events();
            }else{
                Notify.failure(RES.message)
            }
            
        } catch (err) {
            console.log(err)
        }
    }

    onMount(async()=>{

        messages=await get_messages();
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
               Messages List
            </div>
            <div class="" slot="actions">
               <a href="news-and-events/create">
                 <button class="ui mini icon purple basic button">
                    <i class="plus icon"></i> New
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

                            <th>Subject</th>
                            <th>Message</th>

                            <th>Time</th>

                            <th>Status</th>


                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        {#each messages as a,i}
                            <tr class="text-slate-800 text-sm">
                                <td>{i+1}</td>
                                <td>{a.name}</td>
                                <td>
                                    <a class=" text-wekebio-purple" href="mail:{a.email}">{a.email}</a>
                                </td>
                                <td>
                                    <a href="tel:{a.phone}">{a.phone}</a>
                                </td>

                                <td>{a.subject}</td>

                                <td>
                                    <p class="">
                                        {a.message}
                                    </p>
                                </td>
                                <td>{format_date(a.message_time)}</td>
                                <td>{a.status}</td>


                                <td>
                                    <div class="">

                                        <i class="purple check icon  cursor-pointer"></i>

                                        <i class="green send icon   cursor-pointer"></i>

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

