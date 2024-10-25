from fastapi import FastAPI
from routers import example

app = FastAPI()

@app.get("/")
async def root():
    return {"message": "Hello, FastAPI!"}

app.include_router(example.router)
