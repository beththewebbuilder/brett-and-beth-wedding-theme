<?php
get_header();
?>

<div class="background-fade"></div>

<div class="wreath-header">
    <img class="wreath" src="<?php echo get_bloginfo('template_directory'); ?>/assets/wreath.png"/>
    <img class="header-img" src="<?php echo get_bloginfo('template_directory'); ?>/assets/header.jpg"/>
</div>

<!-- <div class="header-hero">
    <div class="header-text bacasime-antique-regular">
        Brett & Beth
    </div>
</div> -->

<div class="text-content">
    <div class="header-txt bacasime-antique-regular">
        Brett & Beth
    </div>
    <div class="invited-text windsong-regular">
        You're invited!
    </div>
    <div class="invite-text inria-serif-bold">
        SATURDAY 13th JUNE 2026
    </div>
    <div class="sub-text inria-serif-light-italic">from</div>
    <div class="invite-text inria-serif-bold">
        6.30pm
    </div>
    <div class="sub-text inria-serif-light-italic">at</div>
    <div class="invite-text inria-serif-bold">
        Example village hall
    </div>
    <a href="#rsvp" class="rsvp-btn btn inria-serif-regular text-center">
        RSVP HERE
    </a>

    <div class="main-text inria-serif-regular">
        <p>We’re having an intimate ceremony and officially becoming Mr & Mrs Luffman on Thursday 4th June 2026.</p>
        <p>But the real celebration comes a little later — on <strong>Saturday 13th June</strong>, we’re throwing a big party with all the people who have touched our lives over the years.
        We’d love for you to come, share a drink, hit the dance floor, and celebrate this next chapter with us.</p>
        <p>We’ll be cutting the cake at 7.30pm and then heading straight into our first dance — so if you don’t want to miss those moments, be sure to join us by then!</p>
    </div>

    <div class="sub-text inria-serif-light-italic">How to get there</div>
    <div class="main-text inria-serif-regular">
        Details on how to get there, address, parking etc
    </div>

    <div class="sub-text inria-serif-light-italic">Things to know</div>
    <div class="main-text inria-serif-regular">
        There will be a bar and a food truck on the night, so bring along some cash/card for food and drinks!
    </div>
</div>

<div class="text-content">
    <div class="invited-text windsong-regular">
        Our Story
    </div>
    <div class="main-text inria-serif-regular">
        <p>We didn’t have the classic <i>“meet-cute”</i> that all of Beth’s romcom marathons had prepared her for. Instead, like 10% of other married couples in the UK <span class="inria-serif-light-italic">(according to Google!)</span>, we met online. Yes — Facebook Dating really is a thing, and it worked for us!</p>
        <p>We clicked straight away, chatting about TV binges, favourite foods, and our shared love of the countryside <span span class="inria-serif-light-italic">(for Brett, Devon in particular!)</span>.</p>
        <p>We met in person for the first time in February 2023 and we both knew we’d stumbled onto something special. A few dates later, Brett introduced Beth to the other leading lady in his life — Heidi — and it was love at first sight all round.</p>
        <p>Fast forward to August 2025: the three of us set off on a Lake District adventure, doing what we love most together — hiking. On one of the very first days, we took on Skiddaw <span span class="inria-serif-light-italic">(the fourth-highest peak in the UK!)</span>. At 3,000 feet, Brett got down on one knee... and the rest, as they say, is history.</p>
    </div>
</div>

<div class="images text-center">
    <img class="inline-image" src="<?php echo get_bloginfo('template_directory'); ?>/assets/proposal.jpg"/>
    <img class="inline-image" src="<?php echo get_bloginfo('template_directory'); ?>/assets/ring.jpg"/>
</div>

<div class="text-content" id="rsvp">
    <div class="invited-text windsong-regular">
        RSVP
    </div>
    <div class="main-text inria-serif-regular">
        We would love for you to join us to celebrate this next step. Please let us know if you can make it or not.
    </div>
</div>
<div class="form-container">
    <form class="inria-serif-regular" id="rsvp_form" novalidate>
        <div class="form-input">
            <label for="name">Your name(s)</label><br/>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="form-input">
            <label for='people'>Number of people you’re RSVP-ing for</label></br>
            <input type="number" min="1" max="10" name="people" id="people" value="2" required>
            <span class="help-block inria-serif-light-italic">(So we have an idea of numbers and to save you filling this out multiple times - don't forget to include yourself!)</span>
        </div>
        <div class="form-input">
            <p>Will you be joining the party?</p>
            <input type="radio" name="response" id="yes" value="yes" checked>
            <label for="yes">🥳 Yes, wouldn't miss it!</label><br/>
            <input type="radio" name="response" id="no" value="no">
            <label for="no">😟 Sorry, can't make it</label>
        </div>
        <div class="form-input">
            <label for='song'>
                What tune will get you on the dance floor?
            </label><br/>
            <input type="text" name="song" id="song">
            <span class="help-block inria-serif-light-italic">We'll try and add it to the playlist!</span>
        </div>
        <div class="form-input">
            <label for='details'>
                Any extra details, things we should know or a message for us?
            </label><br/>
            <textarea name="details" id="details"></textarea>
        </div>
        <div class="form-input text-center">
            <button class="btn inria-serif-regular disabled" type="button" id="send_rsvp_btn">SEND RSVP</button>
        </div>
    </form>
</div>

<div class="form-response-box">
    <div class="form-response-content">
        <div class="yes-response">
            <div class="modal-title bacasime-antique-regular">
                We can't wait to see you!
            </div>
            <div class="modal-content inria-serif-regular">
                Thank you for responding and letting us know you're coming. We're really excited and can't wait to see you there!
            </div>
        </div>
        <div class="no-response">
            <div class="modal-title bacasime-antique-regular">
                We'll miss you!
            </div>
            <div class="modal-content inria-serif-regular">
                Thank you for responding and letting us know you not able to come. We will miss you, but hopefully we can catch-up soon!
            </div>
        </div>
        <div class="windsong-regular love-brett-beth">love, </br> The Luffmans</div>
        <a class="btn inria-serif-regular" id="close-modal" type="button">Close</a>
    </div>
</div>

<div class="loading-background">
    <div class="loading-container">
        <div class="loader"></div>
        <div class="bacasime-antique-regular text-center">Sending...</div>
    </div>
</div>

<?php
get_footer(); 
?>