<script>
	import Segment from "$lib/components/segment.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { post_resource } from "$lib/methods/functions";
	import { org_details } from "$lib/methods/methods";
	import { onMount } from "svelte";
    import pkg from 'notiflix';


    const { Notify, Confirm } = pkg;
    


    const URL=`${API_BASE_URL}update-org.php`;

    const RESOURCE="Organisation";

    let title="Settings";

    let subtitle="View & Manage App Settings";

    let organisation;

    let updating=false;

    const update=async()=>{
        try {

            updating=true;

            let headers={
                'Content-Type':'application/json'
            }

            const RESPONSE=await post_resource(RESOURCE,URL,organisation,headers);

            const RES=RESPONSE.data;

           
            if(RES.success){
                Notify.success(RES.message);

                window.location.reload();

            }else{
                Notify.failure(RES.message)
            }

            updating=false;


        } catch (err) {
            console.log(err)
        }
    }


    onMount(async()=>{
        organisation=await org_details();
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
            <div class="" slot="content">

                {#if organisation}
                <div class="">
                    <form class="ui form" onsubmit={async(e)=>{
                        e.preventDefault();

                       await update();
                    }}>

                        <fieldset class="border border-slate-300 p-2">
                            <legend>Basic Information</legend>

                                <div class="field">
                                    <label for="name">Organisation Name</label>
                                    <input type="text" name="name" id="name" bind:value={organisation.name} required>
                                </div>

                                <div class="flex gap-2 justify-between my-2">
                                    <div class="field">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" bind:value={organisation.phone} placeholder="00 00 000 000" required>
                                    </div>

                                    <div class="field">
                                        <label for="alt-phone">Alt Phone</label>
                                        <input type="text" name="alt-phone" id="alt-phone" bind:value={organisation.alt_phone} placeholder="00 00 000 000">
                                    </div>

                                </div>

                                <div class="flex gap-2 justify-between my-2">
                                    <div class="field">
                                        <label for="email">Email</label>
                                        <input type="email" name="phone" id="phone" bind:value={organisation.email} placeholder="abc@mail.com" required>
                                    </div>

                                    <div class="field">
                                        <label for="alt-email">Alt Email</label>
                                        <input type="email" name="alt-phone" id="alt-email" bind:value={organisation.alt_email} placeholder="alt@mail.com">
                                    </div>

                                </div>
                        </fieldset>

                        <br>

                        <fieldset class="border border-slate-300 p-2 my-2">
                            <legend>Address Details</legend>

                                <div class="field">
                                    <label for="postal-code">Postal Code</label>
                                    <input type="text" name="postal-code" id="postal-code" bind:value={organisation.address.postal_code} required>
                                </div>

                                <div class="flex gap-2 justify-between my-2">
                                    <div class="field">
                                        <label for="county">County</label>
                                        <input type="text" name="county" id="county" bind:value={organisation.address.county} placeholder="00 00 000 000" required>
                                    </div>

                                    <div class="field">
                                        <label for="csubounty">Subcounty</label>
                                        <input type="text" name="subcounty" id="subcounty" bind:value={organisation.address.subcounty} placeholder="00 00 000 000" required>
                                    </div>

                               
                                </div>

                                <div class="flex gap-2 justify-between my-2">

                                    <div class="field">
                                        <label for="constituency">Constituency</label>
                                        <input type="text" name="constituency" id="constituency" bind:value={organisation.address.constituency} placeholder="00 00 000 000">
                                    </div>

                                    <div class="field">
                                        <label for="division">Division</label>
                                        <input type="text" name="division" id="division" bind:value={organisation.address.division} placeholder="abc@mail.com" required>
                                    </div>
                                </div>
                        </fieldset>

                        <br>
                        <fieldset class="border border-slate-300 p-2 my-2">
                            <legend>Extra Details</legend>

                                <div class="field py-4">
                                    <label for="introduction">Introduction</label>
                                    <textarea name="introduction" id="introduction" placeholder="introduction .." bind:value={organisation.detail.introduction} rows="8" required></textarea>
                                </div>


                                <div class="field py-4">
                                    <label for="who-we-are">Who We Are</label>
                                    <textarea name="who-we-are" id="who-we-are" placeholder="who we are .." bind:value={organisation.detail.who_we_are} rows="8" required></textarea>
                                </div>


                                <div class="field py-4">
                                    <label for="goal">Our Goal</label>
                                    <textarea name="goal" id="goal" placeholder="Our goal .." bind:value={organisation.detail.goal} rows="5" required></textarea>
                                </div>

                        </fieldset>


                        <div class="field p-5 text-center">
                            <button class={updating?"ui icon purple loading button":"ui icon purple button"}>
                                <i class="send icon"></i> Update
                            </button>
                        </div>
                    
                    </form>
                </div>
                {/if}

            </div>
        </Segment>
    </div>

</main>

<style>
    input{
        width: 100%;
    }

    textarea{
        width: 100%;
    }

    .field{
        flex:1;
    }
</style>