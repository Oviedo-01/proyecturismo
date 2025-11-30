@extends('layouts.app')

@section('title', 'Inicio - Plataforma Turística')

{{-- Carousel principal --}}
@section('hero')
<div class="container-fluid p-0 mb-5">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="{{ asset('tourism/img/carousel-1.jpg') }}" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Explora Colombia</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Descubre Lugares Increíbles</h1>
                        <a href="{{ route('lugares.explorar') }}" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Ver Lugares</a>
                        <a href="{{ route('register') }}" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Registrarse</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100" src="{{ asset('tourism/img/carousel-2.jpg') }}" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Turismo Cultural</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Vive Experiencias Únicas</h1>
                        <a href="{{ route('lugares.explorar') }}" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Explorar</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
</div>
@endsection

{{-- Contenido principal --}}
@section('content')
<!-- About Start (Sección de texto sin título) -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            {{-- Imagen --}}
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                <div class="position-relative h-100">
                    <img class="img-fluid position-absolute w-100 h-100" src="{{ asset('tourism/img/about-nosotros.jpg') }}" alt="" style="object-fit: cover;">
                </div>
            </div>
            {{-- Texto --}}
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <h1 class="mb-4">Bienvenido a <span class="text-primary">Plataforma Turística</span></h1>
                <p class="mb-4">Descubre los mejores destinos turísticos y culturales. Nuestra plataforma te ayuda a encontrar lugares increíbles, leer reseñas de otros viajeros y planificar tu próxima aventura.</p>
                <div class="row gy-2 gx-4 mb-4">
                    <div class="col-sm-6">
                        <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Lugares Verificados</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Reseñas Reales</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Información Actualizada</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Fácil de Usar</p>
                    </div>
                </div>
                <a class="btn btn-primary py-3 px-5 mt-2" href="{{ route('lugares.explorar') }}">Explorar Lugares</a>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<!-- Service Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Servicios</h6>
            <h1 class="mb-5">Nuestros Servicios</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item rounded pt-3">
                    <div class="p-4">
                        <i class="fa fa-3x fa-globe text-primary mb-4"></i>
                        <h5>Lugares Turísticos</h5>
                        <p>Explora los mejores destinos culturales y turísticos de la región</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item rounded pt-3">
                    <div class="p-4">
                        <i class="fa fa-3x fa-star text-primary mb-4"></i>
                        <h5>Reseñas</h5>
                        <p>Lee y comparte experiencias de otros viajeros</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item rounded pt-3">
                    <div class="p-4">
                        <i class="fa fa-3x fa-map-marker-alt text-primary mb-4"></i>
                        <h5>Ubicaciones</h5>
                        <p>Encuentra lugares con información detallada de ubicación</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="service-item rounded pt-3">
                    <div class="p-4">
                        <i class="fa fa-3x fa-user text-primary mb-4"></i>
                        <h5>Cuenta Personal</h5>
                        <p>Gestiona tus reseñas y lugares favoritos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Service End -->
@endsection