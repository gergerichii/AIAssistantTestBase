from fastapi import FastAPI
from routers import all_routers
app = FastAPI()

@app.get("/")
async def root():
    return {"message": "Hello, FastAPI!"}

for router in all_routers:
    app.include_router(router)
