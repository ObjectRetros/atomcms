import com.eu.habbo.Emulator;
import com.eu.habbo.habbohotel.bots.Bot;
import com.eu.habbo.habbohotel.rooms.Room;
import com.eu.habbo.habbohotel.rooms.RoomTile;
import com.eu.habbo.habbohotel.rooms.RoomChatMessage;
import com.eu.habbo.habbohotel.rooms.RoomChatMessageBubbles;
import com.eu.habbo.habbohotel.users.Habbo;
import com.eu.habbo.messages.outgoing.rooms.users.RoomUserTalkComposer;
import com.eu.habbo.plugin.EventHandler;
import com.eu.habbo.plugin.EventListener;
import com.eu.habbo.plugin.HabboPlugin;
import com.eu.habbo.plugin.events.users.UserTalkEvent;

import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

/**
 * Arcturus Morningstar Plugin: AI NPC Chat
 *
 * This plugin intercepts player chat messages in the configured room,
 * checks if the player is within 1 tile of the AI NPC bot,
 * and forwards the message to the Atom CMS NPC Chat API.
 * The API response is then spoken by the bot in-game.
 *
 * INSTALLATION:
 * 1. Compile this file and place the .jar in the Arcturus plugins folder.
 * 2. Configure the settings in the emulator_settings table:
 *    - npc.chat.api.url = http://127.0.0.1/api/npc/chat
 *    - npc.chat.api.token = (same as NPC_API_TOKEN in .env)
 *    - npc.chat.bot.name = Atlas
 *    - npc.chat.room.id = 208
 *    - npc.chat.distance = 1
 * 3. Restart the emulator.
 */
public class NpcChatPlugin extends HabboPlugin implements EventListener {

    private static final ExecutorService executor = Executors.newFixedThreadPool(4);

    private String apiUrl;
    private String apiToken;
    private String botName;
    private int roomId;
    private int interactionDistance;

    @Override
    public void onEnable() {
        Emulator.getPluginManager().registerEvents(this, this);

        // Load configuration from emulator_settings
        this.apiUrl = Emulator.getConfig().getValue("npc.chat.api.url", "http://127.0.0.1/api/npc/chat");
        this.apiToken = Emulator.getConfig().getValue("npc.chat.api.token", "");
        this.botName = Emulator.getConfig().getValue("npc.chat.bot.name", "Atlas");
        this.roomId = Emulator.getConfig().getInt("npc.chat.room.id", 208);
        this.interactionDistance = Emulator.getConfig().getInt("npc.chat.distance", 1);

        Emulator.getLogging().logStart("[NPC Chat] AI NPC Chat plugin enabled for room " + this.roomId);
    }

    @Override
    public void onDisable() {
        executor.shutdown();
        Emulator.getLogging().logShutDown("[NPC Chat] AI NPC Chat plugin disabled.");
    }

    @Override
    public boolean hasPermission(Habbo habbo, String s) {
        return false;
    }

    @EventHandler
    public void onUserTalk(UserTalkEvent event) {
        if (event.chatMessage == null || event.chatMessage.getMessage().isEmpty()) {
            return;
        }

        Habbo habbo = event.chatMessage.getHabbo();
        if (habbo == null || habbo.getHabboInfo() == null) {
            return;
        }

        Room room = habbo.getHabboInfo().getCurrentRoom();
        if (room == null || room.getId() != this.roomId) {
            return;
        }

        // Find the AI NPC bot in the room
        Bot npcBot = null;
        for (Bot bot : room.getCurrentBots().valueCollection()) {
            if (bot.getName().equalsIgnoreCase(this.botName)) {
                npcBot = bot;
                break;
            }
        }

        if (npcBot == null) {
            return;
        }

        // Check distance between player and bot (Chebyshev distance)
        RoomTile playerTile = habbo.getRoomUnit().getCurrentLocation();
        int dx = Math.abs(playerTile.x - npcBot.getRoomUnit().getCurrentLocation().x);
        int dy = Math.abs(playerTile.y - npcBot.getRoomUnit().getCurrentLocation().y);
        int distance = Math.max(dx, dy);

        if (distance > this.interactionDistance) {
            // Player is too far - ignore this message
            return;
        }

        // Make the bot look at the player
        npcBot.getRoomUnit().lookAtPoint(playerTile);
        room.sendComposer(new RoomUserTalkComposer(
                new RoomChatMessage("...", npcBot.getRoomUnit(), RoomChatMessageBubbles.NORMAL)
        ).compose());

        // Send to API asynchronously to avoid blocking the game thread
        final Bot finalBot = npcBot;
        final String playerMessage = event.chatMessage.getMessage();
        final int userId = habbo.getHabboInfo().getId();
        final String username = habbo.getHabboInfo().getUsername();
        final int playerX = playerTile.x;
        final int playerY = playerTile.y;
        final int botId = finalBot.getId();

        executor.submit(() -> {
            try {
                String response = sendChatToApi(userId, username, botId, playerMessage, playerX, playerY);

                if (response != null && !response.isEmpty()) {
                    // Schedule the bot to speak on the main thread
                    Emulator.getThreading().run(() -> {
                        Room currentRoom = Emulator.getGameEnvironment().getRoomManager().getRoom(roomId);
                        if (currentRoom != null) {
                            Bot bot = null;
                            for (Bot b : currentRoom.getCurrentBots().valueCollection()) {
                                if (b.getId() == botId) {
                                    bot = b;
                                    break;
                                }
                            }
                            if (bot != null) {
                                // Split long messages into multiple chat lines
                                String[] lines = splitMessage(response, 100);
                                for (int i = 0; i < lines.length; i++) {
                                    final Bot chatBot = bot;
                                    final String line = lines[i];
                                    final int delay = i * 1500; // 1.5s between each line
                                    Emulator.getThreading().run(() -> {
                                        currentRoom.sendComposer(new RoomUserTalkComposer(
                                                new RoomChatMessage(line, chatBot.getRoomUnit(), RoomChatMessageBubbles.BOT)
                                        ).compose());
                                    }, delay);
                                }
                            }
                        }
                    });
                }
            } catch (Exception e) {
                Emulator.getLogging().logErrorLine("[NPC Chat] Error: " + e.getMessage());
            }
        });
    }

    /**
     * Send the player's chat message to the Atom CMS NPC Chat API.
     * Uses Gson (bundled with Arcturus) for JSON serialization.
     */
    private String sendChatToApi(int userId, String username, int botId, String message, int playerX, int playerY) {
        try {
            URL url = new URL(this.apiUrl);
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            conn.setRequestProperty("Content-Type", "application/json; charset=UTF-8");
            conn.setRequestProperty("Accept", "application/json");
            conn.setConnectTimeout(10000);
            conn.setReadTimeout(15000);

            if (this.apiToken != null && !this.apiToken.isEmpty()) {
                conn.setRequestProperty("X-NPC-Token", this.apiToken);
            }

            conn.setDoOutput(true);

            // Build JSON payload with Gson
            JsonObject payload = new JsonObject();
            payload.addProperty("user_id", userId);
            payload.addProperty("username", username);
            payload.addProperty("bot_id", botId);
            payload.addProperty("message", message);
            payload.addProperty("player_x", playerX);
            payload.addProperty("player_y", playerY);

            try (OutputStream os = conn.getOutputStream()) {
                byte[] input = payload.toString().getBytes(StandardCharsets.UTF_8);
                os.write(input, 0, input.length);
            }

            int responseCode = conn.getResponseCode();
            if (responseCode == HttpURLConnection.HTTP_OK) {
                try (BufferedReader br = new BufferedReader(new InputStreamReader(conn.getInputStream(), StandardCharsets.UTF_8))) {
                    StringBuilder responseBody = new StringBuilder();
                    String line;
                    while ((line = br.readLine()) != null) {
                        responseBody.append(line);
                    }

                    // Parse response with Gson
                    JsonObject jsonResponse = JsonParser.parseString(responseBody.toString()).getAsJsonObject();

                    boolean success = jsonResponse.has("success") && jsonResponse.get("success").getAsBoolean();
                    if (success && jsonResponse.has("response") && !jsonResponse.get("response").isJsonNull()) {
                        return jsonResponse.get("response").getAsString();
                    }
                }
            } else {
                Emulator.getLogging().logErrorLine("[NPC Chat] API returned status: " + responseCode);
            }
        } catch (Exception e) {
            Emulator.getLogging().logErrorLine("[NPC Chat] API call failed: " + e.getMessage());
        }
        return null;
    }

    /**
     * Split a message into multiple lines of max length.
     */
    private String[] splitMessage(String message, int maxLength) {
        if (message.length() <= maxLength) {
            return new String[]{message};
        }

        int parts = (int) Math.ceil((double) message.length() / maxLength);
        String[] result = new String[parts];
        for (int i = 0; i < parts; i++) {
            int start = i * maxLength;
            int end = Math.min(start + maxLength, message.length());
            result[i] = message.substring(start, end);
        }
        return result;
    }
}
