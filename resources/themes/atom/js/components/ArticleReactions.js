const ArticleReactions = {
    init() {
        document.addEventListener("alpine:init", () => this.startComponent());
    },

    startComponent() {
        window.Alpine.data(
            "reactions",
            (myReactions = [], articleReactions = [], url = "") => ({
                url,
                myReactions,
                articleReactions,
                allReactions: [],
                isAuthenticated: false,

                init() {
                    this.treatArticleReactions();
                    this.allReactions = window.App.defaultReactions;
                    this.isAuthenticated = window.App.isAuthenticated;

                    this.dispatchFlowbiteEvent();
                },

                treatArticleReactions() {
                    let articleReactions = this.articleReactions;

                    this.articleReactions = [];

                    Object.entries(articleReactions).forEach((reactionData) => {
                        let reactionName = reactionData[0],
                            reactions = Object.values(reactionData[1]);

                        this.articleReactions.push({
                            id: this.generateVirtualReactionId(reactionName),
                            name: reactionName,
                            count: reactions.length,
                            users: reactions.map(
                                (reaction) => reaction.user?.username ?? ""
                            ),
                        });
                    });
                },

                toggleReaction(reaction) {
                    if (!this.url.length || !this.isAuthenticated) return;

                    axios.post(this.url, { reaction }).then((response) => {
                        if (!response.data.success) return;

                        if (!response.data.added) {
                            this.removeReaction(
                                reaction,
                                response.data.username
                            );
                            return;
                        }

                        this.addReaction(reaction, response.data.username);
                    });
                },

                addReaction(name, username) {
                    this.myReactions.push(name);

                    let existingReaction = this.getReactionDataFromName(name);

                    if (existingReaction) {
                        existingReaction.count++;
                        existingReaction.users.push(username);
                        return;
                    }

                    this.articleReactions.push({
                        id: this.generateVirtualReactionId(name),
                        name,
                        count: 1,
                        users: [username],
                    });

                    this.dispatchFlowbiteEvent();
                },

                removeReaction(name, username) {
                    this.myReactions.splice(this.myReactions.indexOf(name), 1);

                    let reactionData = this.getReactionDataFromName(name);

                    if (reactionData.count > 1) {
                        reactionData.count--;
                        reactionData.users.splice(
                            reactionData.users.indexOf(username),
                            1
                        );
                        return;
                    }

                    this.$nextTick(() => {
                        this.articleReactions.splice(
                            this.articleReactions.indexOf(reactionData),
                            1
                        );
                    });
                },

                generateVirtualReactionId(name) {
                    return name + Math.floor(Math.random() * 1000);
                },

                canAddReactionFromModal(name) {
                    return (
                        !this.userHasReaction(name) &&
                        !this.articleHasReaction(name)
                    );
                },

                userHasReaction(reaction) {
                    return this.myReactions.includes(reaction.name);
                },

                articleHasReaction(name) {
                    return (
                        typeof this.getReactionDataFromName(name) !==
                        "undefined"
                    );
                },

                getReactionDataFromName(name) {
                    return this.articleReactions.find(
                        (reaction) => reaction.name === name
                    );
                },

                dispatchFlowbiteEvent() {
                    this.$nextTick(() =>
                        document.dispatchEvent(
                            new CustomEvent("reactions:loaded")
                        )
                    );
                },
            })
        );
    },
};

export { ArticleReactions as default };
