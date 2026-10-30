<section class="about__section p-0" id="about">
    <div class="about__wrapper" style="padding: 40px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section__title-wrapper flex-item wow fadeInUp" data-wow-delay=".2s">
                        <div class="section__title v2">
                            <h4 class="subtitle text-center">About Us</h4>
                            <h2 class="title text-center">The Project to Fuel Your Business Growth</h2>
                        </div>
                        <div class="section__title v2">
                            <p class="desc text-center">The SIOPAY project operates at the heart of a dynamic and thriving network within the market. With our comprehensive management application, you gain access to a range of services designed to empower your business, enhance its visibility, and help you establish a robust local network.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="about__figure wow fadeInUp text-center" data-wow-delay=".3s">
                        <img src="landing/assets/img/gallery/cargo.jpg" alt="about-thumb"  style="
        width: 600px;
        height: 350px;
        border-radius: 15px; 
        object-fit: cover; 
      "/>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6" style="padding:20px;">
                    <div class="aboutBox__wrapper wow fadeInUp" data-wow-delay=".3s">
                        <!-- About Icon Box -->
                        <div class="aboutIcon__box">
                            <div class="icon-box">
                                <i class="fa fa-globe fa-3x"></i> <!-- Bigger icon -->
                            </div>
                            <div class="content">
                                <h4 class="title">Global Opportunities</h4>
                                <p class="desc">Access job opportunities across the globe in various industries.</p>
                            </div>
                        </div>
                        <div class="aboutIcon__box">
                            <div class="icon-box">
                                <i class="fa fa-headset fa-3x"></i> <!-- Bigger icon -->
                            </div>
                            <div class="content">
                                <h4 class="title">24/7 Support</h4>
                                <p class="desc">Our support team is available 24/7 to assist you with any queries.</p>
                            </div>
                        </div>
                        <div class="aboutIcon__box">
                            <div class="icon-box">
                                <i class="fa fa-shield-alt fa-3x"></i> <!-- Bigger icon -->
                            </div>
                            <div class="content">
                                <h4 class="title">Secure & Trusted</h4>
                                <p class="desc">We ensure a secure and trustworthy platform for both employers and job seekers.</p>
                            </div>
                        </div>
                        <div class="aboutIcon__box">
                            <div class="icon-box">
                                <i class="fa fa-check-circle fa-3x"></i> <!-- Bigger icon -->
                            </div>
                            <div class="content">
                                <h4 class="title">Easy Application</h4>
                                <p class="desc">Submit applications easily and track your progress on the platform.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<style type="text/css">
#about {
    background: linear-gradient(to right, #a1c4fd, #c2e9fb); /* Light blue gradient */
    padding: 50px 0; /* Adjust padding as needed */
    color: #333; /* Optional: dark text color for better readability */
}

#about .aboutBox__wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 30px; /* Space between icon boxes */
}

#about .aboutIcon__box {
    display: flex;
    align-items: center;
    gap: 15px; /* Space between the icon and text */
}

#about .icon-box {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 60px;
    height: 60px;
    background-color: #ffffff; /* White background for the icon box */
    border-radius: 50%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#about .icon-box i {
    font-size: 30px; /* Adjust icon size */
    color: #2575fc; /* Icon color */
}

#about .content {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

#about .content .title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
}

#about .content .desc {
    font-size: 14px;
    color: #666;
}

#about .about__figure {
    display: flex;
    justify-content: center;  /* Horizontally centers the image */
    align-items: center;      /* Vertically centers the image */
    height: 100%;             /* Ensures the div takes up the full height of its parent */
}

#about .about__figure img {
    max-width: 100%;          /* Ensures the image is responsive */
    height: auto;             /* Maintains the aspect ratio of the image */
}

@media (max-width: 767px) {
#about .icon-box {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 50px;
    height: 50px;
    background-color: #ffffff; /* White background for the icon box */
    border-radius: 50%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 10px;
}

}
</style>



{{-- <section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-info bg-primary text-white p-4 rounded">
                    <h2 class="mb-4">{{ __('contact.1') }}</h2>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt fa-fw mr-2"></i>Piazza Medaglia d'Oro Porcelli
                            88046 Lamezia
                            Terme (CZ) Italy</li>
                        <li class="mb-2"><i class="fas fa-phone-alt fa-fw mr-2"></i>+39 0968 191 6024
                            ({{ __('contact.6') }})</li>
                        <li class="mb-2"><i class="fas fa-mobile-alt fa-fw mr-2"></i>+393770993615
                            ({{ __('contact.7') }})</li>
                        <li class="mb-2"><i class="fas fa-fax fa-fw mr-2"></i>+39 0968 191 6024
                            ({{ __('contact.8') }})</li>
                        <li class="mb-2"><i class="fas fa-envelope fa-fw mr-2"></i>support@siopay.eu</li>
                        <li class="mb-2"><i class="fas fa-id-card fa-fw mr-2"></i>03762610784 (P.Iva)</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-details bg-secondary text-primary p-4 rounded">
                    <h2 class="mb-4">{{ __('contact.2') }}</h2>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="info-item">
                                <h3 class="mb-2" data-toggle="counter-up">225</h3>
                                <p class="font-weight-bold">{{ __('contact.3') }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="info-item">
                                <h3 class="mb-2" data-toggle="counter-up">1050</h3>
                                <p class="font-weight-bold">{{ __('contact.4') }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="info-item">
                                <h3 class="mb-2" data-toggle="counter-up">2500</h3>
                                <p class="font-weight-bold">{{ __('contact.5') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section> --}}
