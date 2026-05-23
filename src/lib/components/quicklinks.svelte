<script>
	import Segment from "./segment.svelte";
    import GroupIcon from "$lib/assets/icons/icons8-user-groups-96.png";
    import ToolIcon from "$lib/assets/icons/icons8-tools-100.png";
    import TrackIcon from "$lib/assets/icons/icons8-tracker-64.png";
    import ChurchIcon from "$lib/assets/icons/icons8-church-60.png";
    import SettingsIcon from "$lib/assets/icons/icons8-settings-94.png";





    let userGroup="admin";

    let links=[
        {
            name:"groups",
            url:`/${userGroup}/groups`,
            icon:GroupIcon
        },
             {
            name:"tools",
            url:`/${userGroup}/tools`,
            icon:ToolIcon
        },
             {
            name:"prayer houses",
            url:`/${userGroup}/houses`,
            icon:ChurchIcon
        },
             {
            name:"tithe track",
            url:`/${userGroup}/apps/tithe-track`,
            icon:TrackIcon
        },
             {
            name:"settings",
            url:`/${userGroup}/settings`,
            icon:SettingsIcon
        }
    ]

</script>
<main>
    <Segment>
    <div class="" slot="title">
        QuickLinks
    </div>
        <div class="" slot="content">
            <div class="flex gap-2 py-2">
                {#each links as ql}
                    <a href="{ql.url}" class="p-2 shadow-md hover:bg-slate-100 m-2">
                        <div class="">
                            <img src="{ql.icon}" alt="ql.name" class="w-18 h-18">
                        </div>
                        <div class="text-center capitalize">
                            {ql.name}
                        </div>
                    </a>
                {/each}
            </div>
        </div>
    </Segment>
</main>