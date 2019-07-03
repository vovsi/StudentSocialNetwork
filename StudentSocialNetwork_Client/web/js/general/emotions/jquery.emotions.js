(function ($) {

    $.emotions = function (text) {
        return $.emotions.parse(text);
    };

    var $t = $.emotions;

    $.extend($.emotions, {
        // https://emojipedia.org/people/
        settings: {
            replacement: '<span title="{eId}" class="emotions emo-{eId}" contentEditable="false"></span>',
            map: {
                "&:1": "grinning",
                "&:2": "smiley",
                "&:3": "smile",
                "&:4": "grin",
                "&:5": "laughing",
                "&:6": "sweat_smile",
                "&:7": "rofl",
                "&:8": "joy",
                "&:9": "slightly_happy",
                "&1:10": "sarcasm",
                "&1:11": "blush",
                "&1:12": "innocent",
                "&1:13": "in_love_face",
                "&1:14": "heart_eyes",
                "&1:15": "face_with_starry_eyes",
                "&1:16": "kissing",
                "&1:17": "relaxed",
                "&1:18": "kissing_closed_eyes",
                "&1:19": "kissing_smiling_eyes",
                "&2:20": "yum",
                "&2:21": "stuck_out_tongue",
                "&2:22": "stuck_out_tongue_winking_eye",
                "&2:23": "goofy_face",
                "&2:24": "stuck_out_tongue_closed_eyes",
                "&2:25": "money_mouth_face",
                "&2:26": "happy_face_with_hugging_hands",
                "&2:27": "blushing_face_with_hand_over_mouth",
                "&2:28": "shushing_face",
                "&2:29": "thinking_face",
                "&3:30": "face_with_a_zipper_mouth",
                "&3:31": "face_with_raised_eyebrow",
                "&3:32": "neutral_face",
                "&3:33": "expressionless",
                "&3:34": "no_mouth",
                "&3:35": "smirk",
                "&3:36": "unamused",
                "&3:37": "face_with_rolling_eyes",
                "&3:38": "grimacing",
                "&3:39": "lying_face",
                "&4:40": "relieved",
                "&4:41": "pensive",
                "&4:42": "sleepy",
                "&4:43": "drooling_face",
                "&4:44": "sleeping",
                "&4:45": "mask",
                "&4:46": "face_with_thermometer",
                "&4:47": "face_with_head_bandage",
                "&4:48": "nauseated_face",
                "&4:49": "face_with_open_mouth_vomiting",
                "&5:50": "sneezing_face",
                "&5:51": "cold_face",
                "&5:52": "woozy_face",
                "&5:53": "dizzy_face",
                "&5:54": "exploding_head",
                "&5:55": "cowboy_face",
                "&5:56": "party_face",
                "&5:57": "sunglasses",
                "&5:58": "nerdy_face",
                "&5:59": "face_with_monocle",
                "&6:60": "confused",
                "&6:61": "worried",
                "&6:62": "slightly_frowning_face",
                "&6:63": "frowning_face",
                "&6:64": "open_mouth",
                "&6:65": "hushed",
                "&6:66": "astonished",
                "&6:67": "flushed",
                "&6:68": "pleading_face",
                "&6:69": "frowning",
                "&7:70": "anguished",
                "&7:71": "fearful",
                "&7:72": "cold_sweat",
                "&7:73": "disappointed_relieved",
                "&7:74": "cry",
                "&7:75": "sob",
                "&7:76": "scream",
                "&7:77": "confounded",
                "&7:78": "persevere",
                "&7:79": "disappointed",
                "&8:80": "sweat",
                "&8:81": "weary",
                "&8:82": "tired_face",
                "&8:83": "triumph",
                "&8:84": "rage",
                "&8:85": "angry",
                "&8:86": "cursing",
                "&8:87": "smiling_imp",
                "&8:88": "imp",
                "&8:89": "skull",
            }
        },
        shortcode: function (eId) {
            var $s = $t.settings;
            for (var pattern in $s.map) {
                if ($s.map[pattern] == eId)
                    return pattern;
            }

            return "";
        },
        parse: function (text) {

            var $s = $t.settings;

            for (var pattern in $s.map) {

                var encPattent = $t.encode(pattern);

                if (text.indexOf(pattern) < 0 && text.indexOf(encPattent) < 0) {
                    continue;
                }

                var rep = $s.replacement
                    .replace(/\{eId\}/g, $s.map[pattern]);

                text = text
                    .replace(new RegExp($t.quote(pattern), "g"), rep)
                    .replace(new RegExp($t.quote(encPattent), "g"), rep);
            }

            return text;
        },
        encode: function (str) {
            return (str + '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        },
        quote: function (str) {
            return (str + '').replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
        }
    });

    $.fn.emotions = function (action, options) {
        this.each(function () {
            var el = $(this);
            el.html($.emotions(el.html()));
        });
    };
})(jQuery);