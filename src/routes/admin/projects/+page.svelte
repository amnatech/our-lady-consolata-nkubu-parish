<script>
	import Segment from "$lib/components/segment.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource, post_resource } from "$lib/methods/functions";
	import { onMount } from "svelte";
    import pkg from 'notiflix';
	import { goto } from "$app/navigation";
	import { current_liu, get_projects } from "$lib/methods/methods";
    
    const { Notify, Confirm } = pkg;
    
    const URL=`${API_BASE_URL}projects.php`;

    const RESOURCE="projects";

    const liu=current_liu();

    let title="projects";

    let subtitle="View And Manage projects";

    let projects=[];

    let showProjectModal=false;

    let selectedProject=null;

    // actions =  create, view, update , delete

    let projectAction="create";

    const delete_project=async(project)=>{

        let dt={
            action:"delete",
            project_id:project.project_id,
            title:project.title,
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

                projects=await get_projects();
            }else{
                Notify.failure(RES.message)
            }
            
        } catch (err) {
            console.log(err)
        }
    }

    onMount(async()=>{

        projects=await get_projects();
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
                Projects List
            </div>
            <div class="" slot="actions">
               <a href="projects/create">
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
                            <th>Year</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        {#each projects as a,i}
                            <tr>
                                <td>{i+1}</td>
                                <td>{a.title}</td>
                                <td>{a.project_year}</td>
                                <td>{a.start_date}</td>
                                <td>{a.end_date}</td>
                                <td>{a.location}</td>
                                <td>
                                    <div class="">
                                        <!-- svelte-ignore a11y_click_events_have_key_events -->
                                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                                        <i  class="red trash icon cursor-pointer" onclick={()=>{
                                            Confirm.show(
                                                "Delete project",
                                                `Delete project ${a.title}? This Action Cannot Be Undone`,
                                                "Yes",
                                                "No",
                                                ()=>{
                                                    delete_project(a);
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

