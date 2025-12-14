<?php

class SentimentService
{
  private array $positiveWords = [
    // Core positives
    'love','loved','loving','like','liked','liking','enjoy','enjoyed','enjoyable','good','great','amazing',
    'awesome','fantastic','excellent','nice','pleasant','positive','wonderful','brilliant','perfect',

    // Satisfaction & emotion
    'happy','happier','happiest','satisfied','content','pleased','delighted','thrilled','excited',
    'motivated','inspired','confident','relaxed','comfortable','relieved','grateful','thankful',

    // Quality & performance
    'helpful','useful','effective','efficient','productive','reliable','stable','accurate','fast',
    'smooth','responsive','organized','structured','clear','clean','polished','professional',

    // Learning & understanding
    'understand','understood','easy','simple','straightforward','clear explanation','easy to follow',
    'informative','educational','insightful','knowledgeable','learning','learned','improved','progress',

    // Engagement & experience
    'fun','enjoyment','engaging','interactive','interesting','captivating','fascinating','exciting',
    'motivating','stimulating','rewarding','immersive',

    // Recommendation & approval
    'recommend','recommended','worth it','valuable','beneficial','impressive','outstanding',
    'top-notch','high quality','best','better','great experience','excellent experience',

    // Speed & convenience
    'quick','faster','convenient','accessible','user-friendly','intuitive','easy to use',

    // Service & people
    'friendly','kind','supportive','approachable','responsive','attentive','patient','helped me',

    // Phrases
    'well done','job well done','works well','no issues','no problem','very good','so good',
    'really good','highly recommend','loved it','happy with','satisfied with'
];

    
    
  

    private array $negativeWords = [
    // Core negatives
    'hate','hated','hating','dislike','disliked','boring','bad','terrible','awful','horrible','worst',
    'useless','worthless','negative','poor','pathetic',

    // Confusion & difficulty
    'confusing','confused','unclear','misleading','hard','harder','hardest','difficult','complicated',
    'overwhelming','hard to understand','did not understand',"don't understand",'lost',

    // Emotional frustration
    'annoying','frustrating','stressful','irritating','tiring','tired','exhausting','disappointing',
    'disappointed','angry','upset','annoyed','burned out',

    // Quality & failure
    'broken','bug','bugs','buggy','error','errors','issue','issues','problem','problems','fail',
    'failed','failure','crash','crashed','unresponsive','unstable','lag','laggy','glitch','glitches',

    // Speed & performance
    'slow','slower','slowest','delay','delayed','loading forever','takes too long',

    // Organization & clarity
    'messy','disorganized','chaotic','rushed','poorly made','badly explained','not clear',
    'poor explanation','inconsistent','inaccurate',

    // Experience
    'boring','dull','uninteresting','tedious','monotonous','repetitive','confusing experience',
    'bad experience','terrible experience',

    // Recommendation rejection
    'not recommend','would not recommend','waste','waste of time','not worth it',

    // Service & people
    'rude','unhelpful','unresponsive','careless','lazy','incompetent',

    // Phrases
    'very bad','really bad','so bad','no help','did not help','made it worse','gave up',
    'gave me a headache','painful','stress inducing'
];


    public function __construct() {}

    public function classifyText(string $text): string
    {
        $text = mb_strtolower($text);
        $tokens = preg_split('/[^a-z]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $pos = 0;
        $neg = 0;

        foreach ($tokens as $token) {
            if (in_array($token, $this->positiveWords, true)) {
                $pos++;
            }
            if (in_array($token, $this->negativeWords, true)) {
                $neg++;
            }
        }

        foreach ($this->negativeWords as $phrase) {
            if (str_contains($phrase, ' ') && str_contains($text, $phrase)) {
                $neg++;
            }
        }

        if ($pos > $neg) {
            return 'positive';
        }

        if ($neg > $pos) {
            return 'negative';
        }

        return 'neutral';
    }
}
