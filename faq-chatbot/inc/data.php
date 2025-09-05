<?php
/**
 * FAQ Chatbot Data Structure
 * 
 * This file contains all the hardcoded FAQ data for the chatbot.
 * To modify the services, questions, or answers, edit this file directly.
 * 
 * Structure:
 * - services: Array of service categories
 *   - id: Unique identifier for the service
 *   - title: Display name for the service
 *   - items: Array of FAQ items for this service
 *     - id: Unique identifier for the FAQ item
 *     - question: The question text displayed to users
 *     - answer: The answer text displayed when question is selected
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

return [
    'services' => [
        [
            'id' => 'dementia-care',
            'title' => __('Dementia & Care', 'faq-chatbot'),
            'items' => [
                [
                    'id' => 'approach',
                    'question' => __('What is your approach to dementia care?', 'faq-chatbot'),
                    'answer' => __('We utilize the Cognitive Therapeutics Method™, which provides one-to-one cognitive stimulation through activities targeting attention span, visual-spatial perception, language, memory, and executive functioning.', 'faq-chatbot')
                ],
                [
                    'id' => 'training',
                    'question' => __('How are your dementia caregivers trained?', 'faq-chatbot'),
                    'answer' => __('Our caregivers receive advanced training in dementia and memory care, pass thorough background checks, and are bonded, licensed, and insured.', 'faq-chatbot')
                ],
                [
                    'id' => 'home-environment',
                    'question' => __('Why is home care beneficial for dementia patients?', 'faq-chatbot'),
                    'answer' => __('Familiar home surroundings help maximize comfort while eliminating feelings of fear, anxiety and restlessness that dementia patients often exhibit in new environments.', 'faq-chatbot')
                ],
                [
                    'id' => 'cost',
                    'question' => __('Is there additional cost for your specialized dementia programs?', 'faq-chatbot'),
                    'answer' => __('No, we provide our Cognitive Therapeutics Method™ at no additional cost to our clients as part of our comprehensive dementia care services.', 'faq-chatbot')
                ],
            ]
        ],
        [
            'id' => 'respite-care',
            'title' => __('Respite & Hourly Care', 'faq-chatbot'),
            'items' => [
                [
                    'id' => 'what-is',
                    'question' => __('What is respite care?', 'faq-chatbot'),
                    'answer' => __('Respite care provides temporary relief for primary caregivers, allowing them time to rest, handle personal matters, or recharge.', 'faq-chatbot')
                ],
                [
                    'id' => 'options',
                    'question' => __('What types of respite care do you offer?', 'faq-chatbot'),
                    'answer' => __('We offer multiple options including in-home care, adult day care center support, residential respite stays, and emergency respite.', 'faq-chatbot')
                ],
                [
                    'id' => 'scheduling',
                    'question' => __('How flexible is your hourly care scheduling?', 'faq-chatbot'),
                    'answer' => __('We offer extremely flexible scheduling with no long-term contracts required. You can schedule care for a few hours a day, a few days a week, or on an as-needed basis.', 'faq-chatbot')
                ],
                [
                    'id' => 'caregiver-quality',
                    'question' => __('How do you ensure caregiver quality?', 'faq-chatbot'),
                    'answer' => __('Only about 1 in 25 caregiver applicants meets our selection criteria. All must pass a PhD-developed psychological examination testing for honesty, kindness and conscientiousness.', 'faq-chatbot')
                ],
            ]
        ],
        [
            'id' => '24-hour-care',
            'title' => __('24 HOUR CARE', 'faq-chatbot'),
            'items' => [
                [
                    'id' => 'what-is',
                    'question' => __('What is 24-hour live-in care?', 'faq-chatbot'),
                    'answer' => __('Live-in or 24-hour care offers seniors the opportunity to age in place in their familiar home environment with around-the-clock support.', 'faq-chatbot')
                ],
                [
                    'id' => 'approach',
                    'question' => __('What makes your 24-hour care approach unique?', 'faq-chatbot'),
                    'answer' => __('We utilize two proprietary methods: The Balanced Care Method™ which emphasizes healthy diet, physical activity, mental stimulation, social ties, and purpose to promote overall wellness.', 'faq-chatbot')
                ],
                [
                    'id' => 'caregiver-restrictions',
                    'question' => __('Are there restrictions on what caregivers can do?', 'faq-chatbot'),
                    'answer' => __('Yes, our live-in caregivers maintain maximum professionalism at all times. They are permitted on the client\'s premises only on their assigned shifts.', 'faq-chatbot')
                ],
                [
                    'id' => 'emergency',
                    'question' => __('What if we need emergency care?', 'faq-chatbot'),
                    'answer' => __('Our Care Managers are available 24/7 to address emergencies. Whether you need immediate care or simply have a question, you can reach out to us at any time.', 'faq-chatbot')
                ],
            ]
        ],
        [
            'id' => 'assisted-living-care',
            'title' => __('Care in Assisted Living', 'faq-chatbot'),
            'items' => [
                [
                    'id' => 'what-is',
                    'question' => __('What is care in assisted living facilities?', 'faq-chatbot'),
                    'answer' => __('We provide supplemental care for seniors residing in assisted living facilities, ensuring they receive additional personalized attention and support beyond what the facility staff can provide.', 'faq-chatbot')
                ],
                [
                    'id' => 'services',
                    'question' => __('What services do you provide in assisted living facilities?', 'faq-chatbot'),
                    'answer' => __('We offer companionship, assistance with daily activities, medication reminders, mobility support, cognitive stimulation activities, and personalized care.', 'faq-chatbot')
                ],
                [
                    'id' => 'benefits',
                    'question' => __('What are the benefits of supplemental care in assisted living?', 'faq-chatbot'),
                    'answer' => __('Our services provide additional one-on-one attention, specialized cognitive care through our Cognitive Therapeutics Method, and peace of mind for families.', 'faq-chatbot')
                ],
                [
                    'id' => 'cost',
                    'question' => __('How do you determine the best care options within our budget?', 'faq-chatbot'),
                    'answer' => __('We work closely with families to understand their budget constraints while designing a care plan that addresses their loved one\'s most important needs.', 'faq-chatbot')
                ],
            ]
        ],
        [
            'id' => 'veterans-care',
            'title' => __('Veterans Care', 'faq-chatbot'),
            'items' => [
                [
                    'id' => 'specialized',
                    'question' => __('What specialized care do you offer veterans?', 'faq-chatbot'),
                    'answer' => __('We provide personalized in-home care tailored to veterans\' unique needs, including assistance with daily tasks, mobility support, companionship, and emotional care.', 'faq-chatbot')
                ],
                [
                    'id' => 'benefits',
                    'question' => __('How do you help veterans access benefits?', 'faq-chatbot'),
                    'answer' => __('Our team assists veterans and their families in navigating care options and accessing VA benefits for home care, including Aid and Attendance benefits.', 'faq-chatbot')
                ],
                [
                    'id' => 'activities',
                    'question' => __('What specific activities do your veteran caregivers assist with?', 'faq-chatbot'),
                    'answer' => __('We help with bathing, dressing, grooming, meal preparation, medication reminders, mobility support, transportation to medical appointments, and companionship.', 'faq-chatbot')
                ],
                [
                    'id' => 'eligibility',
                    'question' => __('Who is eligible for your veteran care services?', 'faq-chatbot'),
                    'answer' => __('We specialize in care for veterans of all ages, though we particularly focus on seniors aged 70+.', 'faq-chatbot')
                ],
            ]
        ],
        [
            'id' => 'hospital-to-home',
            'title' => __('Hospital To Home Care', 'faq-chatbot'),
            'items' => [
                [
                    'id' => 'what-is',
                    'question' => __('What is hospital to home care?', 'faq-chatbot'),
                    'answer' => __('Our hospital to home care service ensures a smooth transition from medical facilities back to home, reducing readmission rates and promoting recovery in a comfortable environment.', 'faq-chatbot')
                ],
                [
                    'id' => 'services',
                    'question' => __('What services are included?', 'faq-chatbot'),
                    'answer' => __('We provide medication management, wound care, mobility assistance, coordination with healthcare providers, and daily living support during the recovery period.', 'faq-chatbot')
                ],
                [
                    'id' => 'timing',
                    'question' => __('How quickly can you arrange care after hospital discharge?', 'faq-chatbot'),
                    'answer' => __('We can typically arrange care within 24 hours notice, and often same-day in emergency situations.', 'faq-chatbot')
                ],
                [
                    'id' => 'qualifications',
                    'question' => __('What qualifications do your transition caregivers have?', 'faq-chatbot'),
                    'answer' => __('Our hospital-to-home caregivers are trained in post-operative care, medication management, and rehabilitation support, with many having nursing or CNA backgrounds.', 'faq-chatbot')
                ],
            ]
        ],
        [
            'id' => '70-plus-care',
            'title' => __('70+ Care', 'faq-chatbot'),
            'items' => [
                [
                    'id' => 'what-is',
                    'question' => __('What is 70+ Care?', 'faq-chatbot'),
                    'answer' => __('Our 70+ Care service provides specialized support for seniors aged 70 and older, focusing on the unique needs and challenges that come with advanced age.', 'faq-chatbot')
                ],
                [
                    'id' => 'services',
                    'question' => __('What services are included in 70+ Care?', 'faq-chatbot'),
                    'answer' => __('We provide assistance with personal care, meal preparation according to dietary needs, medication management, light housekeeping, transportation, and companionship.', 'faq-chatbot')
                ],
                [
                    'id' => 'approach',
                    'question' => __('How does your approach differ for older seniors?', 'faq-chatbot'),
                    'answer' => __('We place additional emphasis on fall prevention, medication management, nutrition for aging bodies, social connection to combat loneliness, and cognitive stimulation.', 'faq-chatbot')
                ],
                [
                    'id' => 'scheduling',
                    'question' => __('What care options are available for 70+ seniors?', 'faq-chatbot'),
                    'answer' => __('We offer flexible scheduling including hourly care, overnight care, live-in care, and 24-hour care to meet the varying needs of older seniors.', 'faq-chatbot')
                ],
            ]
        ],
    ]
];

