import importlib
import pkgutil
from pathlib import Path
from fastapi import APIRouter

all_routers = []

package_dir = Path(__file__).parent

for module_info in pkgutil.iter_modules([str(package_dir)]):
    if module_info.name != "__init__":
        module = importlib.import_module(f"{__name__}.{module_info.name}")

        if hasattr(module, "router") and isinstance(module.router, APIRouter):
            all_routers.append(module.router)