-- =============================================================================
-- AI NPC Chat Plugin - Emulator Settings
-- =============================================================================
-- Run this SQL on your Arcturus database to configure the NPC Chat plugin.
-- Adjust the values to match your Atom CMS URL and NPC configuration.
-- =============================================================================

-- API URL: Points to your Atom CMS NPC Chat endpoint
INSERT INTO `emulator_settings` (`key`, `value`) VALUES
    ('npc.chat.api.url', 'http://127.0.0.1/api/npc/chat')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- API Token: Must match NPC_API_TOKEN in your Atom CMS .env file
INSERT INTO `emulator_settings` (`key`, `value`) VALUES
    ('npc.chat.api.token', '')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Bot Name: The name of the AI NPC bot
INSERT INTO `emulator_settings` (`key`, `value`) VALUES
    ('npc.chat.bot.name', 'Atlas')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Room ID: The room where the NPC is placed
INSERT INTO `emulator_settings` (`key`, `value`) VALUES
    ('npc.chat.room.id', '208')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Interaction Distance: Max tiles away a player can be to interact with the NPC
INSERT INTO `emulator_settings` (`key`, `value`) VALUES
    ('npc.chat.distance', '1')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
