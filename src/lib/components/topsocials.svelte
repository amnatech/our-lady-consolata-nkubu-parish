<script>
	import { socialMedia } from "$lib/providers/urls";

</script>
<main>
    <div class="flex justify-between gap-2 items-center h-[4em]">
        <div class="">
            Stay Connected
        </div>
        <div class="socials flex justify-between gap-2">
            {#each socialMedia as social}
                <div class="soc text-xl">
                   {@html social.icon}
                </div>
            {/each}
        </div>
    </div>
</main>