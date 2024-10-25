FROM python:3.11-slim

ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

RUN apt-get update && apt-get install -yq --no-install-recommends \
    mc \
    gcc \
    g++ \
    build-essential \
    libc-dev \
    python3-dev \
    libffi-dev \
    curl

RUN curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- --default-toolchain none -y
ENV PATH=/root/.cargo/bin:$PATH
RUN rustup toolchain install nightly --allow-downgrade --profile minimal --component clippy
