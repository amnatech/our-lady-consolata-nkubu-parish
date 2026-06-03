<script>
	import Segment from "./segment.svelte";
    import GroupIcon from "$lib/assets/icons/icons8-user-groups-96.png";
    import ToolIcon from "$lib/assets/icons/icons8-tools-100.png";
    import TrackIcon from "$lib/assets/icons/icons8-tracker-64.png";
    import ChurchIcon from "$lib/assets/icons/icons8-church-60.png";
    import SettingsIcon from "$lib/assets/icons/icons8-settings-94.png";

    import EventsIcon from "$lib/assets/icons/icons8-events-64_1.png";
    import CalendarIcon from "$lib/assets/icons/calendar_1.png";
    import AppointmentsIcon from "$lib/assets/icons/icons8-appointment-64.png";

    import MessagesIcon from "$lib/assets/icons/icons8-messages-48.png";
    import ReadingsIcon from "$lib/assets/icons/book.png";
    import AlterIcon from "$lib/assets/icons/altar.png";

    let userGroup="admin";


        let links=[
        {
            name:"calendar",
            url:`/${userGroup}/apps/calendar`,
            icon:CalendarIcon
        },
            {
            name:"mass",
            url:`/${userGroup}/apps`,
            icon:AlterIcon
        },
                
        {
            name:"events",
            url:`/${userGroup}/news-and-events`,
            icon:EventsIcon
        },
            {
            name:"readings",
            url:`/${userGroup}/apps`,
            icon:ReadingsIcon
        },
               
        {
            name:"appointments",
            url:`/${userGroup}/apps/appointments`,
            icon:AppointmentsIcon
        },
            {
            name:"messages",
            url:`/${userGroup}/messages`,
            icon:MessagesIcon
        },
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
            name:"Offerings",
            url:`/${userGroup}/apps/offerings`,
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
        <div class="" slot="content">
            <div class="flex gap-2 justify-between py-2 flex-wrap">
                {#each links as ql}
                    <div class="p-2 shadow-md hover:bg-slate-100 m-2 w-[22%]">
                        <a href="{ql.url}" >
                                <div class="text-center">
                                    <img src="{ql.icon}" alt="ql.name" class="w-14 h-14 m-auto">
                                </div>
                                <div class="text-center capitalize p-2 text-xs">
                                    {ql.name}
                                </div>
                            </a>
                    </div>
 
                {/each}
            </div>
        </div>
    </Segment>
</main>