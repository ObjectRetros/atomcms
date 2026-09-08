export interface paths {
    "/api/v1/status": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getStatus */
        get: operations["getStatus"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/bootstrap": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getBootstrap */
        get: operations["getBootstrap"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/articles": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listArticles */
        get: operations["listArticles"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/articles/{article}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getArticle */
        get: operations["getArticle"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/articles/{article}/comments": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listComments */
        get: operations["listComments"];
        put?: never;
        /** createComment */
        post: operations["createComment"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/comments/{comment}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        post?: never;
        /** deleteComment */
        delete: operations["deleteComment"];
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/articles/{article}/reactions": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /**
         * toggleReaction
         * @description Compatibility toggle. Prefer PUT/DELETE for explicit reaction state; allowed values come from bootstrap.reactions.
         */
        post: operations["toggleReaction"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/articles/{article}/reactions/{reaction}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        /** putReaction */
        put: operations["putReaction"];
        post?: never;
        /** deleteReaction */
        delete: operations["deleteReaction"];
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/users/online": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listOnlineUsers */
        get: operations["listOnlineUsers"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/users": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** searchUsers */
        get: operations["searchUsers"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/users/{user}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getUser */
        get: operations["getUser"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/me": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getMe */
        get: operations["getMe"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/me/account": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        /**
         * updateAccount
         * @description Email changes require current_password; username/motto constraints depend on the selected emulator and hotel settings.
         */
        put: operations["updateAccount"];
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/me/password": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        /** updatePassword */
        put: operations["updatePassword"];
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/me/sessions": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listSessions */
        get: operations["listSessions"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/me/referral-claim": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** claimReferral */
        post: operations["claimReferral"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/me/two-factor": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /**
         * getTwoFactor
         * @description Requires recent password confirmation. Enrollment returns the QR SVG; confirmed credentials never expose the provisioning QR. Never cache this response.
         */
        get: operations["getTwoFactor"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/staff": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listStaff */
        get: operations["listStaff"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/teams": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listTeams */
        get: operations["listTeams"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/leaderboards": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getLeaderboards */
        get: operations["getLeaderboards"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/photos": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listPhotos */
        get: operations["listPhotos"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/applications": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listApplications */
        get: operations["listApplications"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/applications/{position}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getPosition */
        get: operations["getPosition"];
        put?: never;
        /** applyForPosition */
        post: operations["applyForPosition"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/shop": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getShop */
        get: operations["getShop"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/shop/packages/{package}/purchases": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** purchasePackage */
        post: operations["purchasePackage"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/shop/purchases": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listPurchases */
        get: operations["listPurchases"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/shop/vouchers": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** redeemVoucher */
        post: operations["redeemVoucher"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/shop/paypal/orders": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /**
         * createPaypalOrder
         * @description amount is whole major currency units (1–250). Response money amounts are integer minor units. Follow approval_url in a browser; server callbacks/webhooks reconcile payment.
         */
        post: operations["createPaypalOrder"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/shop/paypal/orders/{order}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getPaypalOrder */
        get: operations["getPaypalOrder"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/homes/{user}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getHome */
        get: operations["getHome"];
        /** saveHome */
        put: operations["saveHome"];
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/homes/{user}/widgets/{homeItem}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getHomeWidget */
        get: operations["getHomeWidget"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/home-shop": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getHomeShop */
        get: operations["getHomeShop"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/homes/{user}/inventory": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getHomeInventory */
        get: operations["getHomeInventory"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/homes/{user}/purchases": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** purchaseHomeItem */
        post: operations["purchaseHomeItem"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/homes/{user}/messages": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** postHomeMessage */
        post: operations["postHomeMessage"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/homes/{user}/ratings": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** rateHome */
        post: operations["rateHome"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/badges": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getBadgePrice */
        get: operations["getBadgePrice"];
        put?: never;
        /**
         * buyBadge
         * @description badge_data is a validated GIF data URL; badge dimensions/palette follow the shared badge purchase rules.
         */
        post: operations["buyBadge"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/logo": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** uploadLogo */
        post: operations["uploadLogo"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/rare-values": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listRareValues */
        get: operations["listRareValues"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/rare-values/{value}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getRareValue */
        get: operations["getRareValue"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/client/launch": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /**
         * launchClient
         * @description Issues a fresh SSO ticket after access checks. Do not prefetch, persist, log, or cache the returned URL/token. Flash requires server configuration.
         */
        post: operations["launchClient"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/rules": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listRules */
        get: operations["listRules"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/support": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listSupportCategories */
        get: operations["listSupportCategories"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/support/tickets": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** listTickets */
        get: operations["listTickets"];
        put?: never;
        /** createTicket */
        post: operations["createTicket"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/support/tickets/{ticket}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getTicket */
        get: operations["getTicket"];
        /** updateTicket */
        put: operations["updateTicket"];
        post?: never;
        /** deleteTicket */
        delete: operations["deleteTicket"];
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/support/tickets/{ticket}/toggle-status": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** toggleTicketStatus */
        post: operations["toggleTicketStatus"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/support/tickets/{ticket}/replies": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** replyToTicket */
        post: operations["replyToTicket"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/api/v1/support/replies/{reply}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        post?: never;
        /** deleteReply */
        delete: operations["deleteReply"];
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/sanctum/csrf-cookie": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /**
         * initializeCsrf
         * @description Use credentials: include. Read XSRF-TOKEN and send the URL-decoded value in X-XSRF-TOKEN on subsequent unsafe requests.
         */
        get: operations["initializeCsrf"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/login": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /**
         * login
         * @description Send Accept: application/json. two_factor=true creates only a pending login; complete /two-factor-challenge before accessing private operations.
         */
        post: operations["login"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/logout": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** logout */
        post: operations["logout"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/register": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** register */
        post: operations["register"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/two-factor-challenge": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /**
         * completeTwoFactorLogin
         * @description Supply either code or recovery_code, with the pending-login session cookie.
         */
        post: operations["completeTwoFactorLogin"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/forgot-password": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** requestPasswordReset */
        post: operations["requestPasswordReset"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/reset-password/{token}": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** resetPassword */
        post: operations["resetPassword"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/confirm-password": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** confirmPassword */
        post: operations["confirmPassword"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/confirmed-password-status": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getPasswordConfirmation */
        get: operations["getPasswordConfirmation"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/settings/two-factor-authentication": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** enableTwoFactor */
        post: operations["enableTwoFactor"];
        /** disableTwoFactor */
        delete: operations["disableTwoFactor"];
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/settings/two-factor-authentication/confirm": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** confirmTwoFactorEnrollment */
        post: operations["confirmTwoFactorEnrollment"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/two-factor-authentication": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /**
         * fortifyEnableTwoFactor
         * @description Fortify management alternative; requires recent password confirmation. Prefer Atom enable endpoint for its explicit password input.
         */
        post: operations["fortifyEnableTwoFactor"];
        /** fortifyDisableTwoFactor */
        delete: operations["fortifyDisableTwoFactor"];
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/confirmed-two-factor-authentication": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        get?: never;
        put?: never;
        /** fortifyConfirmTwoFactor */
        post: operations["fortifyConfirmTwoFactor"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/two-factor-qr-code": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getFortifyQrCode */
        get: operations["getFortifyQrCode"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/two-factor-secret-key": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getFortifySecretKey */
        get: operations["getFortifySecretKey"];
        put?: never;
        post?: never;
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
    "/user/two-factor-recovery-codes": {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        /** getRecoveryCodes */
        get: operations["getRecoveryCodes"];
        put?: never;
        /** regenerateRecoveryCodes */
        post: operations["regenerateRecoveryCodes"];
        delete?: never;
        options?: never;
        head?: never;
        patch?: never;
        trace?: never;
    };
}
export type webhooks = Record<string, never>;
export interface components {
    schemas: {
        PublicUser: {
            id: number;
            username: string;
            motto: string;
            look: string;
            online: boolean;
        };
        Money: {
            amount_minor: number;
            currency: string;
        };
        Error: {
            code: string;
            message: string;
            errors?: {
                [key: string]: string[];
            };
            /** @description Present only for vote_required; backend-generated vote destination. */
            vote_url?: string;
        };
        AuthError: {
            code?: string;
            message: string;
            errors?: {
                [key: string]: string[];
            };
        };
        PaginationLinks: {
            first: string;
            last: string;
            prev: string | null;
            next: string | null;
        };
        PaginationMeta: {
            current_page: number;
            from: number | null;
            last_page: number;
            links: {
                url: string | null;
                label: string;
                active: boolean;
                page?: number | null;
            }[];
            path: string;
            per_page: number;
            to: number | null;
            total: number;
        };
        Status: {
            installed: boolean;
            maintenance: boolean;
            /** @enum {string} */
            mode: "full" | "headless";
        };
        Bootstrap: {
            viewer: {
                id: number;
                username: string;
                two_factor_enabled: boolean;
                requires_two_factor: boolean;
                can_access_housekeeping: boolean;
            } | null;
            housekeeping_url: string;
            captcha: {
                recaptcha_enabled: boolean;
                recaptcha_site_key: string | null;
                turnstile_enabled: boolean;
                turnstile_site_key: string | null;
            };
            hotel_name: string;
            hotel_description: string;
            /** @enum {string} */
            mode: "full" | "headless";
            installed: boolean;
            maintenance: boolean;
            online_count: number;
            /** @enum {string} */
            emulator: "arcturus" | "ada";
            features: ("camera-photos" | "rare-values")[];
            locale: string;
            locales: {
                name: string;
                locale: string;
            }[];
            assets: {
                logo: string;
                avatar: string | null;
                badge: string;
            };
            registration: {
                enabled: boolean;
                requires_beta_code: boolean;
            };
            reactions: string[];
            operations: string[];
            currency: string;
            latest_photos: {
                id: number;
                url: string;
                author: components["schemas"]["PublicUser"] | null;
            }[];
            discord_url: string | null;
        };
        Me: {
            id: number;
            username: string;
            motto: string;
            look: string;
            online: boolean;
            mail: string;
            balances: {
                credits: number;
                duckets: number;
                diamonds: number;
                points: number;
            };
            website_balance: components["schemas"]["Money"];
            can_change_name: boolean;
            can_generate_logo: boolean;
            referral_code: string | null;
            referrals_needed: number;
            two_factor_enabled: boolean;
            online_friends: components["schemas"]["PublicUser"][];
        };
        Article: {
            id: number;
            slug: string;
            title: string;
            short_story: string;
            full_story: string;
            image: string | null;
            can_comment: boolean;
            created_at: string | null;
            author?: components["schemas"]["PublicUser"] | null;
        };
        Comment: {
            id: number;
            comment: string;
            created_at: string | null;
            author?: components["schemas"]["PublicUser"] | null;
        };
        Session: {
            agent: {
                is_desktop: boolean;
                platform: string | false;
                browser: string | false;
            };
            ip_address: string | null;
            is_current_device: boolean;
            /** Format: date-time */
            last_active: string;
        };
        TwoFactor: {
            enabled: boolean;
            qr_code: string | null;
            recovery_codes: string[];
        };
        StaffGroup: {
            id: number;
            name: string;
            badge: string | null;
            color: string | null;
            background: string | null;
            description: string | null;
            users: components["schemas"]["PublicUser"][];
        };
        Leaderboards: {
            credits: {
                user: components["schemas"]["PublicUser"];
                value: number;
            }[] | null;
            duckets: {
                user: components["schemas"]["PublicUser"];
                value: number;
            }[] | null;
            diamonds: {
                user: components["schemas"]["PublicUser"];
                value: number;
            }[] | null;
            mostOnline: {
                user: components["schemas"]["PublicUser"];
                value: number;
            }[] | null;
            respectsReceived: {
                user: components["schemas"]["PublicUser"];
                value: number;
            }[] | null;
            achievementScores: {
                user: components["schemas"]["PublicUser"];
                value: number;
            }[] | null;
        };
        Photo: {
            id: number;
            url: string;
            created_at: string | null;
            author: components["schemas"]["PublicUser"] | null;
        };
        Position: {
            id: number;
            /** @enum {string} */
            kind: "rank" | "team";
            name: string | null;
            description: string;
            apply_from: string | null;
            apply_to: string | null;
        };
        ShopPackage: {
            id: number;
            name: string;
            description: string | null;
            image: string | null;
            price: components["schemas"]["Money"];
            stock: number | null;
            is_giftable: boolean;
            available: boolean;
            min_rank: number | null;
            max_rank: number | null;
            limit_per_user: number | null;
            items: {
                id: number;
                name: string;
                image: string | null;
                quantity: number | null;
            }[];
        };
        Shop: {
            categories: {
                id: number;
                name: string;
                slug: string;
                icon: string | null;
            }[];
            packages: components["schemas"]["ShopPackage"][];
        };
        PurchaseReceipt: {
            id: number;
            package_name: string;
            recipient_username: string | null;
            charged: components["schemas"]["Money"];
        };
        Purchase: {
            id: number;
            package_id: number;
            package_name: string | null;
            recipient_username: string | null;
            created_at: string | null;
        };
        PaypalOrder: {
            id: string;
            approval_url: string;
            amount_minor: number;
            currency: string;
        };
        PaypalStatus: {
            id: string;
            status: string | null;
            amount_minor: number;
            currency: string;
            credited_at: string | null;
        };
        HomeDefinition: {
            id: number;
            category_id: number | null;
            /** @enum {string} */
            type: "s" | "n" | "w" | "b";
            name: string;
            image: string | null;
            price: number;
            /** @enum {integer} */
            currency: -1 | 0 | 5 | 101;
            limit: number | null;
            total_bought: number;
            widget_type: string | null;
        };
        HomeItem: {
            id: number;
            x: number;
            y: number;
            z: number;
            placed: boolean;
            is_reversed: boolean;
            theme: string | null;
            extra_data: string | null;
            definition: components["schemas"]["HomeDefinition"] | null;
        };
        Home: {
            user: components["schemas"]["PublicUser"];
            active_background: components["schemas"]["HomeItem"] | null;
            items: components["schemas"]["HomeItem"][];
        };
        HomeShop: {
            categories: {
                id: number;
                name: string;
            }[];
            items: components["schemas"]["HomeDefinition"][];
        };
        HomeRoom: {
            id: number;
            name: string;
            description: string | null;
            state: string;
        };
        HomeBadge: {
            code: string;
            slot: number;
        };
        HomeMessage: {
            id: number;
            content: string;
            created_at: string | null;
            author: components["schemas"]["PublicUser"] | null;
        };
        HomeRating: {
            average: number;
            total: number;
            positive: number;
        };
        HomeWidget: {
            id: number;
            /** @constant */
            type: "my-profile";
            /** @constant */
            supported: true;
            content: components["schemas"]["PublicUser"];
        } | {
            id: number;
            /** @constant */
            type: "my-rooms";
            /** @constant */
            supported: true;
            content: components["schemas"]["HomeRoom"][];
        } | {
            id: number;
            /** @constant */
            type: "my-badges";
            /** @constant */
            supported: true;
            content: {
                items: components["schemas"]["HomeBadge"][];
                current_page: number;
                last_page: number;
                total: number;
            };
        } | {
            id: number;
            /** @constant */
            type: "my-friends";
            /** @constant */
            supported: true;
            content: {
                items: (components["schemas"]["PublicUser"] | null)[];
                current_page: number;
                last_page: number;
                total: number;
            };
        } | {
            id: number;
            /** @constant */
            type: "my-rating";
            /** @constant */
            supported: true;
            content: components["schemas"]["HomeRating"];
        } | {
            id: number;
            /** @constant */
            type: "my-guestbook";
            /** @constant */
            supported: true;
            content: components["schemas"]["HomeMessage"][];
        } | {
            id: number;
            /** @constant */
            type: "my-groups";
            /** @constant */
            supported: false;
            content: null;
        };
        SupportCategory: {
            id: number;
            name: string;
            content: string;
            image_url: string | null;
            button_text: string | null;
            button_url: string | null;
        };
        TicketReply: {
            id: number;
            content: string;
            created_at: string | null;
            author: components["schemas"]["PublicUser"] | null;
        };
        Ticket: {
            id: number;
            category_id: number;
            title: string;
            content: string;
            open: boolean;
            created_at: string | null;
            replies?: components["schemas"]["TicketReply"][];
        };
        RuleCategory: {
            id: number;
            name: string;
            description: string | null;
            badge: string | null;
            rules: {
                id: number;
                paragraph: string;
                rule: string;
            }[];
        };
        RareValue: {
            id: number;
            name: string;
            icon: string;
            credit_value: string | null;
            currency_value: string | null;
            currency_type: number;
        };
        ClientLaunch: {
            /** @enum {string} */
            client: "nitro" | "flash";
            sso: string;
            url: string;
            host?: string;
            port?: string | number;
            external_productdata?: string;
            external_furnidata?: string;
            external_texts?: string;
            external_variables?: string;
            external_figuredata?: string;
            external_figuremap?: string;
            external_override_texts?: string;
            external_override_variables?: string;
        };
    };
    responses: never;
    parameters: never;
    requestBodies: never;
    headers: never;
    pathItems: never;
}
export type $defs = Record<string, never>;
export interface operations {
    getStatus: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Status"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getBootstrap: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Bootstrap"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listArticles: {
        parameters: {
            query?: {
                page?: number;
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Article"][];
                        links: components["schemas"]["PaginationLinks"];
                        meta: components["schemas"]["PaginationMeta"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getArticle: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                article: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Article"];
                        reactions: {
                            [key: string]: number;
                        };
                        my_reactions: string[];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listComments: {
        parameters: {
            query?: {
                page?: number;
            };
            header?: never;
            path: {
                article: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Comment"][];
                        links: components["schemas"]["PaginationLinks"];
                        meta: components["schemas"]["PaginationMeta"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    createComment: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                article: string;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    comment: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Comment"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    deleteComment: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                comment: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    toggleReaction: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                article: string;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    reaction: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            success: boolean;
                            added: boolean;
                            username: string;
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    putReaction: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                article: string;
                reaction: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    deleteReaction: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                article: string;
                reaction: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listOnlineUsers: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["PublicUser"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    searchUsers: {
        parameters: {
            query?: {
                q?: string;
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            username: string;
                            look: string;
                        }[];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getUser: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                user: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["PublicUser"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getMe: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Me"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    updateAccount: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    username?: string;
                    mail: string;
                    motto?: string | null;
                    current_password?: string;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    updatePassword: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    current_password: string;
                    password: string;
                    password_confirmation: string;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listSessions: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Session"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    claimReferral: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getTwoFactor: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["TwoFactor"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listStaff: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["StaffGroup"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listTeams: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["StaffGroup"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getLeaderboards: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Leaderboards"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listPhotos: {
        parameters: {
            query?: {
                page?: number;
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Photo"][];
                        links: components["schemas"]["PaginationLinks"];
                        meta: components["schemas"]["PaginationMeta"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listApplications: {
        parameters: {
            query?: {
                kind?: "rank" | "team";
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Position"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getPosition: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                position: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Position"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    applyForPosition: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                position: number;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    content: string;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getShop: {
        parameters: {
            query?: {
                category?: string;
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Shop"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    purchasePackage: {
        parameters: {
            query?: never;
            header: {
                /** @description Reuse the same key and payload after a network failure. A different payload with this key returns 409. */
                "Idempotency-Key": string;
            };
            path: {
                package: number;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    receiver?: string | null;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["PurchaseReceipt"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listPurchases: {
        parameters: {
            query?: {
                page?: number;
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Purchase"][];
                        links: components["schemas"]["PaginationLinks"];
                        meta: components["schemas"]["PaginationMeta"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    redeemVoucher: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    code: string;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            credited: components["schemas"]["Money"];
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    createPaypalOrder: {
        parameters: {
            query?: never;
            header: {
                /** @description Reuse the same key and payload after a network failure. A different payload with this key returns 409. */
                "Idempotency-Key": string;
            };
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    amount: number;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["PaypalOrder"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getPaypalOrder: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                order: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["PaypalStatus"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getHome: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                user: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Home"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    saveHome: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                user: string;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    items?: {
                        id: number;
                        x: number;
                        y: number;
                        z: number;
                        is_reversed?: boolean | null;
                        theme?: string | null;
                        placed?: boolean | null;
                        extra_data?: string | null;
                    }[] | null;
                    backgroundId: number;
                };
            };
        };
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getHomeWidget: {
        parameters: {
            query?: {
                page?: number;
            };
            header?: never;
            path: {
                user: string;
                homeItem: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["HomeWidget"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getHomeShop: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["HomeShop"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getHomeInventory: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                user: string;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["HomeItem"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    purchaseHomeItem: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                user: string;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    item_id: number;
                    quantity: number;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["HomeDefinition"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    postHomeMessage: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                user: string;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    content: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    rateHome: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                user: string;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    rating: number;
                };
            };
        };
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getBadgePrice: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            cost: number;
                            currency: string;
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    buyBadge: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    badge_data: string;
                    badge_name: string;
                    badge_description: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            badge_url: string;
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    uploadLogo: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "multipart/form-data": {
                    /** Format: binary */
                    logo: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            url: string;
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listRareValues: {
        parameters: {
            query?: {
                search?: string;
                category?: number;
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            id: number;
                            name: string;
                            values: components["schemas"]["RareValue"][];
                        }[];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getRareValue: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                value: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            id: number;
                            name: string;
                            icon: string;
                            credit_value: string | null;
                            currency_value: string | null;
                            currency_type: number;
                            holdings: {
                                user: components["schemas"]["PublicUser"] | null;
                                count: number;
                            }[];
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    launchClient: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    /** @enum {string} */
                    client?: "nitro" | "flash";
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["ClientLaunch"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listRules: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["RuleCategory"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listSupportCategories: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["SupportCategory"][];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    listTickets: {
        parameters: {
            query?: {
                page?: number;
                all?: boolean;
            };
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Ticket"][];
                        links: components["schemas"]["PaginationLinks"];
                        meta: components["schemas"]["PaginationMeta"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    createTicket: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    category_id: number;
                    title: string;
                    content: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Ticket"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    getTicket: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                ticket: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["Ticket"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    updateTicket: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                ticket: number;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    category_id: number;
                    title: string;
                    content: string;
                };
            };
        };
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    deleteTicket: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                ticket: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    toggleTicketStatus: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                ticket: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    replyToTicket: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                ticket: number;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    content: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            id: number;
                            content: string;
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    deleteReply: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                reply: number;
            };
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["Error"];
                };
            };
        };
    };
    initializeCsrf: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    login: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    username: string;
                    password: string;
                    remember?: boolean;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        two_factor: boolean;
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    logout: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    register: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    username: string;
                    mail: string;
                    password: string;
                    password_confirmation: string;
                    referral_code?: string | null;
                    beta_code?: string | null;
                    terms: boolean;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": string;
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    completeTwoFactorLogin: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    code?: string;
                    recovery_code?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    requestPasswordReset: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    mail: string;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        message: string;
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    resetPassword: {
        parameters: {
            query?: never;
            header?: never;
            path: {
                token: string;
            };
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    password: string;
                    password_confirmation: string;
                    "g-recaptcha-response"?: string;
                    "cf-turnstile-response"?: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        message: string;
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    confirmPassword: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    password: string;
                };
            };
        };
        responses: {
            /** @description Success */
            201: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": string;
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    getPasswordConfirmation: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        confirmed: boolean;
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    enableTwoFactor: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    current_password: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: components["schemas"]["TwoFactor"];
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    disableTwoFactor: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    current_password: string;
                };
            };
        };
        responses: {
            /** @description Success */
            204: {
                headers: {
                    [name: string]: unknown;
                };
                content?: never;
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    confirmTwoFactorEnrollment: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    code: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        data: {
                            enabled: boolean;
                        };
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    fortifyEnableTwoFactor: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": "";
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    fortifyDisableTwoFactor: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": "";
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    fortifyConfirmTwoFactor: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody: {
            content: {
                "application/json": {
                    code: string;
                };
            };
        };
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": "";
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    getFortifyQrCode: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        svg: string;
                        url: string;
                    } | unknown[];
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    getFortifySecretKey: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": {
                        secretKey: string;
                    };
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    getRecoveryCodes: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": string[];
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
    regenerateRecoveryCodes: {
        parameters: {
            query?: never;
            header?: never;
            path?: never;
            cookie?: never;
        };
        requestBody?: never;
        responses: {
            /** @description Success */
            200: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": "";
                };
            };
            /** @description Request rejected. Inspect code when present; validation details are keyed by field. */
            default: {
                headers: {
                    [name: string]: unknown;
                };
                content: {
                    "application/json": components["schemas"]["AuthError"];
                };
            };
        };
    };
}
