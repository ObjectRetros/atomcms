<x-content.content-section icon="discord-icon" classes="border dark:border-gray-900">
    <x-slot:title>
        {{ __('Discord') }}
    </x-slot:title>

    <x-slot:under-title>
        <span id="guildName"></span>
    </x-slot:under-title>

    <div class=" text-sm dark:text-gray-200">
        <div id="guildUsers" class="h-[129px] overflow-auto"> </div>
        <a id="guildInvite" href="https://discord.gg/7jftX65hYV" target="_blank">
            <x-form.secondary-button classes="mt-3">
                {{ __('Join server') }}
            </x-form.secondary-button>
        </a>
    </div>
</x-content.content-section>

@push('javascript')
    <script>
        window.onload = DiscordApi();

        function DiscordApi() {
            let init = {
                method: 'GET',
                mode: 'cors',
                cache: 'reload'
            }
            //gets discord widget json from url with in settings specifed id
            fetch("https://discordapp.com/api/guilds/{{ setting('discord_widget_id') }}/widget.json", init).then(
                function(res) {
                    //if there is a problem with discord or id sends an error message in console
                    if (res.status != 200) {
                        console.error("Discord widget cant connect to discord (" + res.status + ")");
                        return;
                    }

                    res.json().then(function(data) {
                        let users = data.members;
                        let guildName = data.name;
                        //sets the subtitle of the card to the guild name
                        document.getElementById('guildName').innerText = guildName;

                        //loops over every user in json array and display them in the widget
                        for (let i = 0; i < data.members.length; i++) {
                            let container = document.createElement('div')
                            let leftContainer = document.createElement('div')
                            let imgContainer = document.createElement('div')
                            let img = document.createElement('img')
                            let status = document.createElement('div')
                            let rightContainer = document.createElement('div')
                            let name = document.createElement('p')
                            let motto = document.createElement('p')

                            //sets styleing
                            container.classList.add('flex', 'items-center', 'gap-x-2')
                            leftContainer.classList.add('relative')
                            imgContainer.classList.add('h-9', 'w-9', 'bg-gray-100', 'dark:bg-gray-800',
                                'rounded-full', 'flex', 'items-center', 'justify-center', 'overflow-hidden')
                            status.classList.add('absolute', 'bottom-0', 'right-0', 'w-3', 'h-3',
                                'rounded-full', 'border-2', 'dark:border-gray-800')
                            name.classList.add('font-semibold')
                            motto.classList.add('dark:text-gray-400')

                            //sets styling for exceptions
                            if (i === 0) {
                                name.classList.add('mt-1')
                            }
                            if (i !== 0) {
                                imgContainer.classList.add('mt-1')
                                name.classList.add('mt-3')
                            }
                            if (users[i].status === 'online') {
                                status.style.backgroundColor = "#16a34a";
                            }
                            if (users[i].status === 'idle') {
                                status.style.backgroundColor = "#e9b124";
                            }
                            if (users[i].status === 'dnd') {
                                status.style.backgroundColor = "#9c0017";
                            }

                            //adds attributes to elements
                            img.setAttribute('src', data.members[i].avatar_url);

                            if (users[i].nick === undefined) {
                                name.innerText = users[i].username;
                            } else {
                                name.innerText = users[i].nick;
                            }
                            if (users[i].game !== undefined) {
                                motto.innerText = users[i].game.name;
                            }

                            //append all elements to each other
                            container.appendChild(leftContainer)
                            leftContainer.appendChild(imgContainer)
                            imgContainer.appendChild(img)
                            leftContainer.appendChild(status)
                            container.appendChild(rightContainer)
                            rightContainer.appendChild(name)
                            rightContainer.appendChild(motto)

                            document.getElementById('guildUsers').appendChild(container)
                        }

                        // Always use our invite link regardless of Discord widget data
                        document.getElementById('guildInvite').setAttribute('href', 'https://discord.gg/7jftX65hYV')
                    })
                });
        }
    </script>
@endpush
